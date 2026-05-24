<?php

// Exemple pedagogique simplifie: en production, creer un Voter Symfony complet.
if ($invoice->getUser() !== $this->getUser() && !$this->isGranted('ROLE_ADMIN')) {
    throw $this->createAccessDeniedException();
}
