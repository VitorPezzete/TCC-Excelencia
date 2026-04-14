<footer class="bg-[#0a0504] text-gray-300 py-16 border-t-8 border-secondary" id="contato">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-12">
            <div class="col-span-1 lg:col-span-1">
                <a class="flex items-center gap-3 mb-6" href="#">
                    <span class="font-display font-bold text-3xl text-secondary">Excelência</span>
                </a>
                <p class="text-sm text-gray-400 mb-6 leading-relaxed">
                    Doces e salgados artesanais, preparados com os melhores ingredientes para adoçar o seu dia. Prazer
                    em servir!
                </p>
                <!-- <div class="flex space-x-4"> -->
                <!-- <a class="w-10 h-10 rounded-full bg-secondary/20 flex items-center justify-center text-secondary hover:bg-secondary hover:text-primary transition-colors" href="#"> -->
                <!-- <span class="material-icons">facebook</span> -->
                <!-- </a> -->
                <!-- <a class="w-10 h-10 rounded-full bg-secondary/20 flex items-center justify-center text-secondary hover:bg-secondary hover:text-primary transition-colors" href="#"> -->
                <!-- <span class="material-icons">camera_alt</span> -->
                <!-- </a> -->
                <!-- </div> -->
            </div>
            <div>
                <h4 class="font-display text-xl font-bold text-white mb-6 flex items-center gap-2">
                    <span class="w-4 h-px bg-secondary"></span> Links Úteis
                </h4>
                <ul class="space-y-3">
                    <li><a class="hover:text-secondary transition-colors text-sm" href="#inicio">Início</a></li>
                    <li><a class="hover:text-secondary transition-colors text-sm" href="#historia">Nossa História</a>
                    </li>
                    <li><a class="hover:text-secondary transition-colors text-sm"
                            href="{{ route('cardapio') }}">Cardápio</a></li>
                </ul>
            </div>
            <div>
                <h4 class="font-display text-xl font-bold text-white mb-6 flex items-center gap-2">
                    <span class="w-4 h-px bg-secondary"></span> Contato
                </h4>
                <ul class="space-y-4">
                    <li class="flex items-start gap-3">
                        <span class="material-icons text-secondary text-xl">location_on</span>
                        <span class="text-sm">Rua José Bonifácio, 380 e 462 - Centro<br />São Vicente, SP</span>
                    </li>
                    <li class="flex items-center gap-3">
                        <span class="material-icons text-secondary text-xl">phone</span>
                        <span class="text-sm">(13) 3467-4305</span>
                    </li>
                </ul>
            </div>
            <div>
                <h4 class="font-display text-xl font-bold text-white mb-6 flex items-center gap-2">
                    <span class="w-4 h-px bg-secondary"></span> Horário
                </h4>
                <ul class="space-y-3">
                    <li class="flex justify-between border-b border-gray-800 pb-2">
                        <span class="text-sm">Seg - Sab</span>
                        <span class="text-sm text-secondary font-bold">07:00 - 19:00</span>
                    </li>
                    <li class="flex justify-between border-b border-gray-800 pb-2">
                        <span class="text-sm">Domingo</span>
                        <span class="text-sm text-red-400 font-bold">Fechado</span>
                    </li>
                </ul>
            </div>
        </div>
        <div class="mt-16 pt-8 border-t border-gray-800 text-center text-sm text-gray-500">
            <p>© 2003 Excelência Doces & Salgados. Todos os direitos reservados.</p>
        </div>
    </div>
</footer>