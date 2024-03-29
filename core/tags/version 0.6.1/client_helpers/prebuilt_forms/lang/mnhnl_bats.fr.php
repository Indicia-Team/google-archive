<?php
/**
 * Indicia, the OPAL Online Recording Toolkit.
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * any later version.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see http://www.gnu.org/licenses/gpl.html.
 *
 * @package	Client
 * @author	Indicia Team
 * @license	http://www.gnu.org/licenses/gpl.html GPL 3.0
 * @link 	http://code.google.com/p/indicia/
 */

global $custom_terms;

/**
 * Language terms for the survey_reporting_form_2 form.
 *
 * @package	Client
 */
$custom_terms = array(
	'Edit' => '�diter',
	// 'Actions' is left unchanged
	// TBD translations for report grid headings.
	// TBD Translations for species grid headings, species tab header, species comment header, conditions block headers.
	'LANG_Edit' => '�diter',
	'LANG_Add_Sample' => 'Ajouter Nouvel �chantillon',
	'LANG_Add_Sample_Single' => 'Add Unique',
	'LANG_Add_Sample_Grid' => 'Ajouter plusieurs occurrences',
	// 'Site' tab heading left alone
	'Existing Locations' => 'Sites existants',
	'LANG_Location_Code_Label' => 'Code',
	'LANG_Location_Code_Blank_Text' => 'Choisissez un emplacement existant par le code',
	'LANG_Location_Name_Label' => 'Nom du site',
	'LANG_Location_Name_Blank_Text' => 'Choisissez un emplacement existant par nom',
	'Create New Location' => 'Cr�er un nouvel emplacement',
	'village' => 'Village / Lieu-dit',
	'site type' => 'Type de g�te',
	'site followup' => 'Pertinence du site pour un suivi r�gulier',
	'LANG_SRef_Label' => 'Coordonn�es',
	'LANG_Georef_Label'=>'Chercher la position sur la carte',
	'LANG_Georef_SelectPlace' => 'Choisissez la bonne parmi les localit�s suivantes qui correspondent � votre recherche. (Cliquez dans la liste pour voir l\'endroit sur la carte.)',
	'LANG_Georef_NothingFound' => 'Aucun endroit n\'a �t� trouv� avec ce nom. Essayez avec le nom d\'une localit� voisine.',
	'search' => 'Chercher',
	'Location Comment' => 'Commentaires',

	'LANG_Tab_otherinformation' => 'Conditions',
	'LANG_Date' => 'Date',
	'General' => 'G�n�ral',
	'Physical' => 'Caract�ristiques de la cavit�',
	'Microclimate' => 'Conditions microclimatiques',
	'Visit' => 'Visite',
	'Bat Visit' => 'Visite',
	'Observers' => 'Observateur(s)',
	'cavity entrance' => 'Entr�e de la cavit�',
	'disturbances' => 'Perturbations',
	'Human Frequentation' => 'Fr�quentation humaine du site',
	'Bats Temp Exterior' => "Temp�rature � l'ext�rieur de la cavit� (Celcius)",
	'Bats Humid Exterior' => "Humidit� relative hors de la cavit� (%)",
	'Bats Temp Int 1' => "Temp�rature � l'int�rieur de la cavit� - A (Celcius)",
	'Bats Humid Int 1' => "Humidit� � l'int�rieur de la cavit� - A (%)",
	'Bats Temp Int 2' => "Temp�rature � l'int�rieur de la cavit� - B (Celcius)",
	'Bats Humid Int 2' => "Humidit� � l'int�rieur de la cavit� - B (%)",
	'Bats Temp Int 3' => "Temp�rature � l'int�rieur de la cavit� - C (Celcius)",
	'Bats Humid Int 3' => "Humidit� � l'int�rieur de la cavit� - C (%)",
	'Positions Marked' => 'Emplacement(s) des prises de mesures indiqu�(s) sur le relev� topographique',
	'Bats Reliability' => "Fiabilit� (exhaustivit�) de l'inventaire",
	'Overall Comment' => 'Commentaires',

	'LANG_Tab_species' => 'Esp�ces',
	'species_checklist.species'=>'Esp�ces',
	'excrement' => 'Excr�ments',
	'corpse' => 'Cadavre(s)',
	'sleepy' => 'L�thargie',
	'Number: previous area' => 'Nombre d�individus: Partie ant�rieurement explor�e',
	'Number: new area' => 'Nombre d�individus: Partie nouvellement explor�e',
	'No Observation' => 'Aucune observation',
	'Comment' => 'Commentaires',

	'validation_required' => 'Veuillez entrer une valeur pour ce champ',
	'validation_max' => "S'il vous pla�t entrer une valeur inf�rieure ou �gale � {0}.",
	'validation_min' => "S'il vous pla�t entrez une valeur sup�rieure ou �gale � {0}.",
	'validation_no_observation' => "Le <strong>Aucune observation</strong> doit �tre coch�e si et seulement si il n'existe pas de donn�es dans la grille des esp�ces."

);