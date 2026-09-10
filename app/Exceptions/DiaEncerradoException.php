<?php

namespace App\Exceptions;

/**
 * Lancada quando se tenta criar/editar/excluir um RegistroMedicacao cuja
 * 'data' ja foi encerrada em App\Models\DiarioStatus. E a barreira final da
 * regra de imutabilidade, caso algo escape da Policy/UI.
 */
class DiaEncerradoException extends \RuntimeException
{
    public function __construct(string $data)
    {
        parent::__construct("O diario do dia {$data} ja foi encerrado e nao pode mais ser alterado.");
    }
}
