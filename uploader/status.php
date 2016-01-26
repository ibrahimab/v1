<?php

use Chalet\DeveloperNotifier;

if (true === $vars['mongodb']['wrapper']->hasError()) {

    echo '<strong>Op dit moment is het niet mogelijk om afbeeldingen te uploaden. Er wordt een melding gestuurd naar het ontwikkelteam.</strong>';

    $notifier = new DeveloperNotifier($vars['mongodb']['wrapper']->getException());
    $notifier->setEmail($vars['development_team_mail']);
    $notifier->setUsername($login->username);
    $notifier->setSubject('MongoDB server - Afbeeldingensysteem niet te gebruiken');
    $notifier->setUrl($vars['basehref'] . substr($_SERVER['REQUEST_URI'], 1));
    $notifier->send($vars['website']);

    return true;
}

return false;