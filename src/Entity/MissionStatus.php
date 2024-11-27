<?php

namespace App\Entity;

enum MissionStatus: string
{
    case PENDING = 'EN ATTENTE';
    case IN_PROGRESS = 'COMMENCÉE';
    case CANCELLED = 'ANNULÉE';
    case COMPLETED = 'FINIE';
    case FAILED = 'ÉCHOUÉE';
    
}


?>