<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\Endereco;

class ProfileController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $addresses = $user->enderecos()->get();
        $pedidos = $user->pedidos()->with(['itens.produto', 'pagamento', 'avaliacao'])->latest()->get();
        return view('perfil', compact('user', 'addresses', 'pedidos'));
    }

    public function updateData(Request $request)
    {
        $request->validate([
            'name'  => 'required|string',
            'phone' => 'required',
        ]);

        Auth::user()->update([
            'name'  => $request->name,
            'phone' => $request->phone,
        ]);

        return back()->with('success_dados', 'Dados atualizados com sucesso!');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'new_password'     => 'required|min:8|confirmed',
        ]);

        if (!Hash::check($request->current_password, Auth::user()->password)) {
            return back()->withErrors(['current_password' => 'Senha atual incorreta.']);
        }

        Auth::user()->update([
            'password' => bcrypt($request->new_password),
        ]);

        return back()->with('success_senha', 'Senha atualizada com sucesso!');
    }

    public function storeAddress(Request $request)
    {
        $request->validate([
            'name'         => 'required|string',
            'cep'          => 'required',
            'number'       => 'required',
        ]);

        $isDefault = Auth::user()->enderecos()->count() === 0;

        Auth::user()->enderecos()->create([
            'nome'         => $request->name,
            'cep'          => $request->cep,
            'numero'       => $request->number,
            'complemento'   => $request->complement,
            'padrao'   => $isDefault,
        ]);

        return back()->with('success_endereco', 'Endereço adicionado com sucesso!');
    }

    public function destroyAddress($id)
    {
        $address = Endereco::where('id', $id)
                          ->where('user_id', Auth::id())
                          ->firstOrFail();
        $address->delete();

        return back()->with('success_endereco', 'Endereço removido com sucesso!');
    }

    public function setDefaultAddress($id)
    {
        Auth::user()->enderecos()->update(['padrao' => false]);

        Endereco::where('id', $id)
               ->where('user_id', Auth::id())
               ->update(['padrao' => true]);

        return back()->with('success_endereco', 'Endereço padrão atualizado!');
    }

    public function updateAddress(Request $request, $id)
{
    $request->validate([
        'name'         => 'required|string',
        'cep'          => 'required',
        'number'       => 'required',
    ]);

    Endereco::where('id', $id)
           ->where('user_id', Auth::id())
           ->update([
               'nome'         => $request->name,
               'cep'          => $request->cep,
               'numero'       => $request->number,
               'complemento'   => $request->complement,
           ]);

    return back()->with('success_endereco', 'Endereço atualizado com sucesso!');
}

    }