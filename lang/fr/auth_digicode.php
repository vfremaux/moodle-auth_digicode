<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Strings for component 'auth_digicode', language 'en'.
 *
 * @package   auth_digicode
 * @copyright 1999 onwards Martin Dougiamas  {@link http://moodle.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['digicode:managesessions'] = 'Gérer les sessions d\'accès rapide';

$string['configexpiration'] = 'Expiration de la session';
$string['configexpiration_desc'] = 'Referme l\'activation de l\'accès par digicode après un certain temps.';
$string['configgeneratepredelay'] = 'Pre-delai de génération';
$string['configinstructions'] = 'Instructions';
$string['configinstructions_desc'] = 'Des instructions pour guider les utilisateurs';
$string['configlength'] = 'Longueur';
$string['configlength_desc'] = 'Détermine la longueur du code pour les générations futures (n\'afectte pas les codes déjà existants).';
$string['configopentime'] = 'Heure d\'ouverture';
$string['configopentime_desc'] = 'Définit une date et heure d\'activation du digicode';
$string['configrestrictioncontext'] = 'Contexte de restriction';
$string['configrestrictioncontext_desc'] = 'Le type de contexte sur lequel la restriction sera testée';
$string['configrestrictionid'] = 'Identifiant de la cible';
$string['configrestrictionid_desc'] = 'L\'identifiant (nom court, code) de la cible selon son type (role, capacité, ou champ de profil)';
$string['configrestrictiontype'] = 'Type de restriction';
$string['configrestrictiontype_desc'] = 'Le type de la cible de la restriction';
$string['configrestrictionvalue'] = 'Valeur ou filtre de restriction';
$string['configrestrictionvalue_desc'] = 'Une valeur littérale ou une expression de filtre pour passer la restriction';
$string['configencodedigicodes'] = 'Encoder les digicodes (Pro)';
$string['course'] = 'Cours cible';
$string['digicode'] = 'Composez votre code';
$string['digicode_settings'] = 'Accès par digicode';
$string['duration'] = 'Durée de session';
$string['generatecodes'] = 'Générer des codes pour les participants';
$string['hasrestrictions'] = 'Cette session est pour un public restreint';
$string['instructions'] = 'Instructions';
$string['invalidsessions'] = 'Session invalide';
$string['login'] = 'Se connecter';
$string['managesessions'] = 'Gérer des sessions d\'accès';
$string['minutes'] = '{$a} minutes';
$string['neverexpires'] = 'N\'expire jamais';
$string['newsession'] = 'Nouvelle session';
$string['nosessions'] = 'Aucune session';
$string['othersessioncollides'] = 'Une autre session recoupe cette plage horaire';
$string['otherusers'] = 'Autres utilisateurs';
$string['passwordfailure'] = 'Mot de passe incorrect.';
$string['pluginname'] = 'Accès par digicode';
$string['preopentime'] = 'Pré-délai de présentation';
$string['profiling'] = 'Profilage';
$string['restrictioncontextlevel'] = 'Contexte de restriction';
$string['restrictionid'] = 'Identifiant de critère de restriction';
$string['restrictiontype'] = 'Type de restriction';
$string['restrictionvalue'] = 'Valeur ou filtre de restriction';
$string['runnow'] = 'Lancer le générateur de code maintenant !';
$string['cgrunning'] = 'Génération en cours';
$string['cgcompleted'] = 'Génération terminée';
$string['cgwaiting'] = 'La génération de codes est en attente d\'exécution pour le {$a}.';
$string['sendcodes'] = 'Envoyer les codes aux participants';
$string['session'] = 'Session d\'accès par digicode';
$string['sessions'] = 'Sessions d\'accès par digicode';
$string['sessiontime'] = 'Début de session';
$string['sessiontarget'] = 'Cours cible';
$string['setdigicode'] = 'changer mon digicode';

$string['restrictiontype:none'] = 'Aucune restriction';
$string['restrictiontype:profilefield'] = 'Champ de profil utilisateur';
$string['restrictiontype:role'] = 'Rôle dans le contexte';
$string['restrictiontype:capability'] = 'Capacité dans le contexte';

$string['restrictioncontext:user'] = 'Utilisateur';
$string['restrictioncontext:site'] = 'Page d\'accueil';
$string['restrictioncontext:system'] = 'Site';
$string['restrictioncontext:course'] = 'Cours (cours cible)';

$string['auth_digicodedescription'] = 'Cette méthode est similaire à une méthode local manuelle, mais elle est optimisée pour
garantir un temps de mise en connexion le plus court possible.';

$string['newdigicode_subject'] = 'Votre digicode a changé sur {$a}';
$string['newdigicode_tpl'] = '
Site: <%%SITE%%>
--------------------------------------

<%%USERNAME%%>,

Votre digicode d\'accès a changé : <%%DG%%>

Vous pourrez l\'utiiser lors de prochaines sessions d\'accès rapide sur ce site.
Mémorisez le et conservez le dans un endroit sûr et confidentiel.

';

$string['newdigicode_html_tpl'] = '
<b>Site:</b> <%%SITE%%>
<hr>

<p><%%USERNAME%%>,</p>

<p>Votre digicode d\'accès a changé : <b><%%DG%%></b></p>

<p>Vous pourrez l\'utiiser lors de prochaines sessions d\'accès rapide sur ce site.<br/>
Mémorisez le et conservez le dans un endroit sûr et confidentiel.</p>

';

$string['configgeneratepredelay_desc'] = 'Le delai d\'anticipation (en heures) avec lequel la tâche de génération et de mise à jour des digicodes sera lancée,
par rapport au début des sessions d\'accès.';

$string['configencodedigicodes_desc'] = 'Si actif, encode les digicodes en base de données. Cet encodage est plus léger,
moins robuste, mais beaucoup plus rapide et suffisamment efficace par rapport à un hashage de mot de passe standard.';

include(__DIR__.'/pro_additional_strings.php');