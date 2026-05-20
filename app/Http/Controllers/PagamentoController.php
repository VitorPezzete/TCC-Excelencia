<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\Pedido;
use App\Models\Pagamento;
use MercadoPago\Client\Payment\PaymentClient;
use MercadoPago\MercadoPagoConfig;

class PagamentoController extends Controller
{
    /**
     * Configura o SDK do Mercado Pago com o Access Token da aplicação.
     */
    private function configurarSDK(): void
    {
        MercadoPagoConfig::setAccessToken(config('mercadopago.access_token'));
        MercadoPagoConfig::setRuntimeEnviroment(MercadoPagoConfig::LOCAL);
    }

    /**
     * Gera um pagamento PIX via API do Mercado Pago para o pedido informado.
     *
     * POST /pagamento/pix/{pedido}
     */
    public function criarPix(Request $request, int $pedidoId)
    {
        // Busca o pedido garantindo que pertence ao usuário logado
        $pedido = Pedido::where('id', $pedidoId)
            ->where('user_id', auth()->id())
            ->with(['pagamento', 'user'])
            ->firstOrFail();

        $pagamento = $pedido->pagamento;

        // Valida que o método registrado é pix
        if (!$pagamento || $pagamento->metodo !== 'pix') {
            return response()->json(['success' => false, 'message' => 'Este pedido não utiliza PIX.'], 422);
        }

        // Se já existe um QR Code válido, devolve o mesmo (evita duplicatas)
        if ($pagamento->mp_payment_id && $pagamento->pixValido()) {
            return response()->json([
                'success'          => true,
                'qr_code'          => $pagamento->pix_qr_code,
                'qr_code_base64'   => $pagamento->pix_qr_code_base64,
                'expiracao'        => $pagamento->pix_expiracao?->toIso8601String(),
                'mp_payment_id'    => $pagamento->mp_payment_id,
            ]);
        }

        $this->configurarSDK();

        $expiracaoMinutos = (int) config('mercadopago.pix_expiration_minutes', 30);
        $dataExpiracao    = now()->addMinutes($expiracaoMinutos)->format('Y-m-d\TH:i:s.000O');

        try {
            $client = new PaymentClient();

            $body = [
                'transaction_amount' => (float) $pedido->total,
                'description'        => "Pedido #{$pedido->id} — Excelência",
                'payment_method_id'  => 'pix',
                'date_of_expiration' => $dataExpiracao,
                'payer'              => [
                    'email'     => $pedido->user->email,
                    'first_name'=> $pedido->user->name,
                ],
                'metadata'           => [
                    'pedido_id' => $pedido->id,
                ],
            ];

            $pagamentoMP = $client->create($body);

            // Extrai os dados do QR Code da resposta
            $qrCode       = $pagamentoMP->point_of_interaction->transaction_data->qr_code        ?? null;
            $qrCodeBase64 = $pagamentoMP->point_of_interaction->transaction_data->qr_code_base64 ?? null;

            // Atualiza o registro de pagamento no banco
            $pagamento->update([
                'status'             => 'pendente',
                'mp_payment_id'      => (string) $pagamentoMP->id,
                'pix_qr_code'        => $qrCode,
                'pix_qr_code_base64' => $qrCodeBase64,
                'pix_expiracao'      => now()->addMinutes($expiracaoMinutos),
            ]);

            return response()->json([
                'success'        => true,
                'qr_code'        => $qrCode,
                'qr_code_base64' => $qrCodeBase64,
                'expiracao'      => now()->addMinutes($expiracaoMinutos)->toIso8601String(),
                'mp_payment_id'  => (string) $pagamentoMP->id,
            ]);

        } catch (\Exception $e) {
            Log::error('Mercado Pago PIX error', [
                'pedido_id' => $pedido->id,
                'message'   => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Não foi possível gerar o PIX no momento. Tente novamente.',
            ], 500);
        }
    }

    /**
     * Webhook do Mercado Pago — atualiza o status do pagamento/pedido.
     *
     * POST /webhook/mercadopago
     * (sem middleware auth — chamado pelo servidor do MP)
     */
    public function webhook(Request $request)
    {
        $tipo = $request->input('type');
        $id   = $request->input('data.id');

        // Só processa notificações de pagamento
        if ($tipo !== 'payment' || !$id) {
            return response()->json(['ok' => true]);
        }

        try {
            $this->configurarSDK();
            $client     = new PaymentClient();
            $pagamentoMP = $client->get($id);

            $status = match ((string) $pagamentoMP->status) {
                'approved'   => 'aprovado',
                'rejected'   => 'rejeitado',
                'cancelled'  => 'cancelado',
                default      => 'pendente',
            };

            // Busca o pagamento local pelo ID do MP
            $pagamento = Pagamento::where('mp_payment_id', (string) $id)->first();

            if ($pagamento) {
                $pagamento->update(['status' => $status]);

                // Atualiza o status do pedido quando aprovado
                if ($status === 'aprovado') {
                    $pagamento->pedido()->update(['status' => 'confirmado']);
                }
            }

        } catch (\Exception $e) {
            Log::error('MP Webhook error', ['id' => $id, 'message' => $e->getMessage()]);
        }

        // Sempre retorna 200 para o MP não reenviar
        return response()->json(['ok' => true]);
    }
}
