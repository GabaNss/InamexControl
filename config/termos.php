<?php

use Illuminate\Support\Carbon;

return [

    /*
    |--------------------------------------------------------------------------
    | Versao vigente do termo de uso/confidencialidade
    |--------------------------------------------------------------------------
    |
    | Ao publicar uma nova versao do termo (resources/views/legal/termos.blade.php),
    | incremente 'versao' e atualize 'publicado_em' para a data da nova versao.
    | O middleware AceiteTermos reexige o aceite de quem aceitou antes dessa data.
    |
    */

    'versao' => env('TERMOS_VERSAO', '1.0'),

    // Data/hora de publicacao da versao vigente. Usuarios com
    // termos_aceitos_em anterior a esta data precisam aceitar novamente.
    'publicado_em' => Carbon::parse(env('TERMOS_PUBLICADO_EM', '2026-11-12 00:00:00')),

];
