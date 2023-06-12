<?php

namespace App\Enums;

enum EmployeeStatus: string
{
    case ACTIF = 'A';
    case CONGE = 'C';
    case INACTIF = 'I';
}
