-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : jeu. 07 mai 2026 à 20:32
-- Version du serveur : 10.4.32-MariaDB
-- Version de PHP : 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `ecole_paiement_db`
--

-- --------------------------------------------------------

--
-- Structure de la table `annees_academiques`
--

CREATE TABLE `annees_academiques` (
  `id` int(11) NOT NULL,
  `libelle` varchar(20) NOT NULL,
  `date_debut` date NOT NULL,
  `date_fin` date NOT NULL,
  `actif` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `annees_academiques`
--

INSERT INTO `annees_academiques` (`id`, `libelle`, `date_debut`, `date_fin`, `actif`, `created_at`) VALUES
(1, '2026-2027', '2026-09-01', '2027-07-31', 1, '2026-05-04 13:13:10'),
(2, 'année scolaire', '2026-09-02', '2026-07-02', 0, '2026-05-06 22:02:07');

-- --------------------------------------------------------

--
-- Structure de la table `calendrier_paiement`
--

CREATE TABLE `calendrier_paiement` (
  `id` int(11) NOT NULL,
  `libelle` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `date_debut` date NOT NULL,
  `date_fin` date NOT NULL,
  `annee_id` int(11) NOT NULL,
  `actif` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `calendrier_paiement`
--

INSERT INTO `calendrier_paiement` (`id`, `libelle`, `description`, `date_debut`, `date_fin`, `annee_id`, `actif`, `created_at`) VALUES
(1, 'année scolaire', 'une année de grâce', '2025-09-04', '2026-07-02', 1, 1, '2026-05-04 20:47:27'),
(2, 'année scolaire', 'nklkhlkh', '2026-09-04', '2027-07-02', 1, 1, '2026-05-07 12:01:00');

-- --------------------------------------------------------

--
-- Structure de la table `calendrier_tranches`
--

CREATE TABLE `calendrier_tranches` (
  `id` int(11) NOT NULL,
  `calendrier_id` int(11) NOT NULL,
  `numero` int(11) NOT NULL COMMENT 'Numéro de la tranche (1,2,3...)',
  `libelle` varchar(200) NOT NULL COMMENT 'Ex: 1ère tranche, 2ème tranche...',
  `pourcentage` decimal(5,2) NOT NULL COMMENT 'Pourcentage du montant total (ex: 30%)',
  `date_limite` date NOT NULL COMMENT 'Date limite de paiement',
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `calendrier_tranches`
--

INSERT INTO `calendrier_tranches` (`id`, `calendrier_id`, `numero`, `libelle`, `pourcentage`, `date_limite`, `description`, `created_at`) VALUES
(1, 2, 1, 'premiertanche', 10.00, '2027-01-01', 'debut', '2026-05-07 12:01:00'),
(2, 2, 2, 'deuxième tranche', 30.00, '2027-03-03', 'second semestre', '2026-05-07 12:01:00'),
(3, 2, 3, 'troixieme tranche', 60.00, '2027-06-30', 'finissage', '2026-05-07 12:01:00');

-- --------------------------------------------------------

--
-- Structure de la table `classes`
--

CREATE TABLE `classes` (
  `id` int(11) NOT NULL,
  `nom` varchar(50) NOT NULL,
  `niveau` varchar(20) NOT NULL,
  `option_id` int(11) NOT NULL,
  `capacite` int(11) DEFAULT 50,
  `actif` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `classes`
--

INSERT INTO `classes` (`id`, `nom`, `niveau`, `option_id`, `capacite`, `actif`, `created_at`) VALUES
(1, '7ème A', '7ème', 1, 50, 1, '2026-05-04 13:13:10'),
(2, '7ème B', '7ème', 2, 50, 1, '2026-05-04 13:13:10'),
(3, '8ème A', '8ème', 1, 50, 1, '2026-05-04 13:13:10');

-- --------------------------------------------------------

--
-- Structure de la table `eleves`
--

CREATE TABLE `eleves` (
  `id` int(11) NOT NULL,
  `matricule` varchar(20) NOT NULL,
  `nom` varchar(100) NOT NULL,
  `postnom` varchar(100) DEFAULT NULL,
  `prenom` varchar(100) NOT NULL,
  `genre` enum('M','F') NOT NULL,
  `date_naissance` date DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `telephone` varchar(20) DEFAULT NULL,
  `adresse` text DEFAULT NULL,
  `nom_parent` varchar(100) DEFAULT NULL,
  `telephone_parent` varchar(20) DEFAULT NULL,
  `email_parent` varchar(100) DEFAULT NULL,
  `actif` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `eleves`
--

INSERT INTO `eleves` (`id`, `matricule`, `nom`, `postnom`, `prenom`, `genre`, `date_naissance`, `email`, `telephone`, `adresse`, `nom_parent`, `telephone_parent`, `email_parent`, `actif`, `created_at`) VALUES
(2, 'ELE-2026-00001', 'KASEREKA', 'KAWA', 'Néhémie', 'M', '2003-10-10', 'kaserekakawanehemie@gmail.com', '+243980772245', 'kantine', 'kagho siviholya', '+243988548432', 'katungukawa@gmail.com', 0, '2026-05-04 17:56:08'),
(3, 'ELE-2026-00002', 'KASEREKA', 'KAWA', 'Néhémie', 'F', '1988-10-22', 'katungukawa@gmail.com', '+243988548432', 'beni', 'kagho siviholya', '+243988548432', 'katungukawa@gmail.com', 1, '2026-05-04 19:22:26'),
(4, 'ELE-2026-00003', 'KAWA', 'KAKULE', 'Josue', 'M', '2007-08-30', 'kakulekawajosue@gmail.com', '+243813613468', 'CANTINE', 'kasereka kawa', '+243980443246', 'kaserekakawanehemie@gmail.com', 1, '2026-05-06 21:12:03'),
(5, 'ELE-2026-00004', 'KAWA', 'KAKULE', 'Josue', 'M', '2007-08-30', 'kakulekawajosue@gmail.com', '+243813613468', 'CANTINE', 'kasereka kawa', '+243980443246', 'kaserekakawanehemie@gmail.com', 1, '2026-05-06 21:16:07');

-- --------------------------------------------------------

--
-- Structure de la table `frais`
--

CREATE TABLE `frais` (
  `id` int(11) NOT NULL,
  `type_frais_id` int(11) NOT NULL,
  `classe_id` int(11) NOT NULL,
  `semestre_id` int(11) NOT NULL,
  `calendrier_id` int(11) DEFAULT NULL,
  `montant` decimal(10,2) NOT NULL,
  `libelle` varchar(200) NOT NULL,
  `date_limite` date DEFAULT NULL,
  `statut` enum('actif','inactif') DEFAULT 'actif',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `frais`
--

INSERT INTO `frais` (`id`, `type_frais_id`, `classe_id`, `semestre_id`, `calendrier_id`, `montant`, `libelle`, `date_limite`, `statut`, `created_at`) VALUES
(1, 2, 1, 1, 1, 27000.00, 'chaque eleve doit être inscrit à une année', '2026-01-05', 'actif', '2026-05-04 20:50:18'),
(2, 1, 1, 1, 2, 380000.00, 'année scolaire', '2027-06-30', 'actif', '2026-05-07 12:03:24');

-- --------------------------------------------------------

--
-- Structure de la table `inscriptions`
--

CREATE TABLE `inscriptions` (
  `id` int(11) NOT NULL,
  `eleve_id` int(11) NOT NULL,
  `classe_id` int(11) NOT NULL,
  `annee_id` int(11) NOT NULL,
  `date_inscription` date NOT NULL,
  `statut` enum('actif','abandonne','diplome','exclu') DEFAULT 'actif',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `inscriptions`
--

INSERT INTO `inscriptions` (`id`, `eleve_id`, `classe_id`, `annee_id`, `date_inscription`, `statut`, `created_at`) VALUES
(1, 2, 1, 1, '2026-05-04', 'actif', '2026-05-04 17:56:08'),
(2, 3, 1, 1, '2026-05-04', 'actif', '2026-05-04 19:22:26'),
(3, 4, 1, 1, '2026-05-06', 'actif', '2026-05-06 21:12:03'),
(5, 5, 1, 1, '2026-05-06', 'actif', '2026-05-06 21:16:07');

-- --------------------------------------------------------

--
-- Structure de la table `logs`
--

CREATE TABLE `logs` (
  `id` int(11) NOT NULL,
  `utilisateur_id` int(11) DEFAULT NULL,
  `action` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `notifications`
--

CREATE TABLE `notifications` (
  `id` int(11) NOT NULL,
  `type` enum('paiement','rappel','echeance','information') NOT NULL,
  `destinataire_email` varchar(100) NOT NULL,
  `sujet` varchar(200) NOT NULL,
  `message` text NOT NULL,
  `envoye` tinyint(1) DEFAULT 0,
  `date_envoi` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `notifications`
--

INSERT INTO `notifications` (`id`, `type`, `destinataire_email`, `sujet`, `message`, `envoye`, `date_envoi`, `created_at`) VALUES
(1, 'paiement', 'katungukawa@gmail.com', 'Confirmation de paiement - institut technique saint comboni', '<html><body><h2>Confirmation de Paiement</h2><p>Bonjour,</p><p>Nous confirmons la réception du paiement pour l\'élève <strong>KASEREKA Néhémie</strong>.</p><table border=\"1\" cellpadding=\"5\" cellspacing=\"0\"><tr><td><strong>Montant payé :</strong></td><td>10,000.00 FC</td></tr><tr><td><strong>Solde restant :</strong></td><td>17,000.00 FC</td></tr></table><p>Merci de votre confiance.</p><p>Cordialement,<br>institut technique saint comboni</p></body></html>', 0, NULL, '2026-05-04 21:41:07'),
(2, 'paiement', 'katungukawa@gmail.com', 'Confirmation de paiement - institut technique saint comboni', '<html><body><h2>Confirmation de Paiement</h2><p>Bonjour,</p><p>Nous confirmons la réception du paiement pour l\'élève <strong>KASEREKA Néhémie</strong>.</p><table border=\"1\" cellpadding=\"5\" cellspacing=\"0\"><tr><td><strong>Montant payé :</strong></td><td>9,000.00 FC</td></tr><tr><td><strong>Solde restant :</strong></td><td>8,000.00 FC</td></tr></table><p>Merci de votre confiance.</p><p>Cordialement,<br>institut technique saint comboni</p></body></html>', 0, NULL, '2026-05-04 21:51:33'),
(3, '', 'email@example.com', 'Notification inscription', 'Notification automatique', 1, '2026-05-06 23:16:26', '2026-05-06 21:16:26'),
(4, 'paiement', 'email@example.com', 'Notification paiement', 'Notification automatique', 1, '2026-05-06 23:23:25', '2026-05-06 21:23:25'),
(5, 'paiement', 'kaserekakawanehemie@gmail.com', 'Confirmation de paiement - institut technique saint comboni', '<html><body><h2>Confirmation de Paiement</h2><p>Bonjour,</p><p>Nous confirmons la réception du paiement pour l\'élève <strong>KAWA Josue</strong>.</p><table border=\"1\" cellpadding=\"5\" cellspacing=\"0\"><tr><td><strong>Montant payé :</strong></td><td>27,000.00 FC</td></tr><tr><td><strong>Solde restant :</strong></td><td>0.00 FC</td></tr></table><p>Merci de votre confiance.</p><p>Cordialement,<br>institut technique saint comboni</p></body></html>', 0, NULL, '2026-05-06 21:23:25'),
(6, 'paiement', 'kaserekakawanehemie@gmail.com', 'Notification automatique', 'Paiement de 380,000.00 FC - Solde: 0.00 FC', 1, '2026-05-07 14:11:49', '2026-05-07 12:11:49'),
(7, 'paiement', 'kakulekawajosue@gmail.com', 'Notification automatique', 'Paiement de 380,000.00 FC - Solde: 0.00 FC', 1, '2026-05-07 14:11:49', '2026-05-07 12:11:49'),
(8, 'paiement', 'kaserekakawanehemie@gmail.com', 'Confirmation de paiement - institut technique saint comboni', '<html><body><h2>Confirmation de Paiement</h2><p>Bonjour,</p><p>Nous confirmons la réception du paiement pour l\'élève <strong>KAWA Josue</strong>.</p><table border=\"1\" cellpadding=\"5\" cellspacing=\"0\"><tr><td><strong>Montant payé :</strong></td><td>380,000.00 FC</td></tr><tr><td><strong>Solde restant :</strong></td><td>0.00 FC</td></tr></table><p>Merci de votre confiance.</p><p>Cordialement,<br>institut technique saint comboni</p></body></html>', 0, NULL, '2026-05-07 12:11:50');

-- --------------------------------------------------------

--
-- Structure de la table `options`
--

CREATE TABLE `options` (
  `id` int(11) NOT NULL,
  `nom` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `actif` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `options`
--

INSERT INTO `options` (`id`, `nom`, `description`, `actif`, `created_at`) VALUES
(1, 'Scientifique', 'Option Math-Physique', 1, '2026-05-04 13:13:10'),
(2, 'Littéraire', 'Option Latin-Philo', 1, '2026-05-04 13:13:10'),
(3, 'Technique', 'Option Technique', 1, '2026-05-04 13:13:10');

-- --------------------------------------------------------

--
-- Structure de la table `paiements`
--

CREATE TABLE `paiements` (
  `id` int(11) NOT NULL,
  `inscription_id` int(11) NOT NULL,
  `frais_id` int(11) NOT NULL,
  `montant_paye` decimal(10,2) NOT NULL,
  `date_paiement` datetime NOT NULL,
  `mode_paiement` enum('espece','mobile_money','virement','cheque','carte') NOT NULL,
  `reference` varchar(100) DEFAULT NULL,
  `statut` enum('en_attente','valide','annule','rembourse') DEFAULT 'valide',
  `commentaire` text DEFAULT NULL,
  `recu_numero` varchar(50) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `paiements`
--

INSERT INTO `paiements` (`id`, `inscription_id`, `frais_id`, `montant_paye`, `date_paiement`, `mode_paiement`, `reference`, `statut`, `commentaire`, `recu_numero`, `created_by`, `created_at`) VALUES
(1, 2, 1, 10000.00, '2026-05-04 23:41:06', 'espece', '1', 'valide', 'commentaire', 'RECU-2026-000001', 1, '2026-05-04 21:41:06'),
(2, 2, 1, 9000.00, '2026-05-04 23:51:33', 'espece', '2', 'valide', 'GGFVHVN', 'RECU-2026-000002', 1, '2026-05-04 21:51:33'),
(3, 5, 1, 27000.00, '2026-05-06 23:22:41', 'espece', '43', 'valide', 'courage', 'RECU-2026-000003', 1, '2026-05-06 21:22:41'),
(5, 5, 2, 380000.00, '2026-05-07 14:11:02', 'espece', '2', 'valide', 'ghkggb', 'RECU-2026-000005', 1, '2026-05-07 12:11:02');

-- --------------------------------------------------------

--
-- Structure de la table `roles`
--

CREATE TABLE `roles` (
  `id` int(11) NOT NULL,
  `nom` varchar(50) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `roles`
--

INSERT INTO `roles` (`id`, `nom`, `description`, `created_at`) VALUES
(1, 'admin', 'Accès total au système', '2026-05-04 13:13:07'),
(2, 'caissier', 'Gestion des paiements', '2026-05-04 13:13:07'),
(3, 'secretaire', 'Gestion des inscriptions et élèves', '2026-05-04 13:13:07'),
(4, 'comptable', 'Gestion financière', '2026-05-04 13:13:07');

-- --------------------------------------------------------

--
-- Structure de la table `semestres`
--

CREATE TABLE `semestres` (
  `id` int(11) NOT NULL,
  `nom` varchar(50) NOT NULL,
  `date_debut` date NOT NULL,
  `date_fin` date NOT NULL,
  `annee_id` int(11) NOT NULL,
  `actif` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `semestres`
--

INSERT INTO `semestres` (`id`, `nom`, `date_debut`, `date_fin`, `annee_id`, `actif`) VALUES
(1, 'Semestre 1', '2026-09-01', '2026-12-31', 1, 1),
(2, 'Semestre 2', '2027-01-01', '2027-07-31', 1, 1);

-- --------------------------------------------------------

--
-- Structure de la table `types_frais`
--

CREATE TABLE `types_frais` (
  `id` int(11) NOT NULL,
  `libelle` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `actif` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `types_frais`
--

INSERT INTO `types_frais` (`id`, `libelle`, `description`, `actif`) VALUES
(1, 'Minerval', 'Frais de scolarité principal', 1),
(2, 'Inscription', 'Frais d\'inscription', 1),
(3, 'Réinscription', 'Frais de réinscription', 1),
(4, 'Examen', 'Frais d\'examen', 1),
(5, 'Bibliothèque', 'Frais de bibliothèque', 1),
(6, 'Assurance', 'Assurance scolaire', 1),
(7, 'Transport', 'Frais de transport', 1);

-- --------------------------------------------------------

--
-- Structure de la table `utilisateurs`
--

CREATE TABLE `utilisateurs` (
  `id` int(11) NOT NULL,
  `nom` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `mot_de_passe` varchar(255) NOT NULL,
  `telephone` varchar(20) DEFAULT NULL,
  `actif` tinyint(1) DEFAULT 1,
  `derniere_connexion` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `utilisateurs`
--

INSERT INTO `utilisateurs` (`id`, `nom`, `email`, `mot_de_passe`, `telephone`, `actif`, `derniere_connexion`, `created_at`, `updated_at`) VALUES
(1, 'nehemie', 'kaserekakawanehemie@gmail.com', '$2y$10$G0bNeHBwz3XtNAFDxAHiReah0hCzOLOETe85JzoFP650.zkH/Yyfy', '+243980443246', 1, NULL, '2026-05-04 13:13:10', '2026-05-07 10:21:04');

-- --------------------------------------------------------

--
-- Structure de la table `utilisateur_roles`
--

CREATE TABLE `utilisateur_roles` (
  `utilisateur_id` int(11) NOT NULL,
  `role_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `utilisateur_roles`
--

INSERT INTO `utilisateur_roles` (`utilisateur_id`, `role_id`) VALUES
(1, 1);

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `annees_academiques`
--
ALTER TABLE `annees_academiques`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `libelle` (`libelle`);

--
-- Index pour la table `calendrier_paiement`
--
ALTER TABLE `calendrier_paiement`
  ADD PRIMARY KEY (`id`),
  ADD KEY `annee_id` (`annee_id`);

--
-- Index pour la table `calendrier_tranches`
--
ALTER TABLE `calendrier_tranches`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_calendrier` (`calendrier_id`),
  ADD KEY `idx_date_limite` (`date_limite`);

--
-- Index pour la table `classes`
--
ALTER TABLE `classes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `option_id` (`option_id`);

--
-- Index pour la table `eleves`
--
ALTER TABLE `eleves`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `matricule` (`matricule`);

--
-- Index pour la table `frais`
--
ALTER TABLE `frais`
  ADD PRIMARY KEY (`id`),
  ADD KEY `type_frais_id` (`type_frais_id`),
  ADD KEY `classe_id` (`classe_id`),
  ADD KEY `semestre_id` (`semestre_id`),
  ADD KEY `calendrier_id` (`calendrier_id`);

--
-- Index pour la table `inscriptions`
--
ALTER TABLE `inscriptions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_inscription` (`eleve_id`,`annee_id`),
  ADD KEY `classe_id` (`classe_id`),
  ADD KEY `annee_id` (`annee_id`);

--
-- Index pour la table `logs`
--
ALTER TABLE `logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `utilisateur_id` (`utilisateur_id`);

--
-- Index pour la table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `options`
--
ALTER TABLE `options`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `paiements`
--
ALTER TABLE `paiements`
  ADD PRIMARY KEY (`id`),
  ADD KEY `inscription_id` (`inscription_id`),
  ADD KEY `frais_id` (`frais_id`),
  ADD KEY `created_by` (`created_by`);

--
-- Index pour la table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `nom` (`nom`);

--
-- Index pour la table `semestres`
--
ALTER TABLE `semestres`
  ADD PRIMARY KEY (`id`),
  ADD KEY `annee_id` (`annee_id`);

--
-- Index pour la table `types_frais`
--
ALTER TABLE `types_frais`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `utilisateurs`
--
ALTER TABLE `utilisateurs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Index pour la table `utilisateur_roles`
--
ALTER TABLE `utilisateur_roles`
  ADD PRIMARY KEY (`utilisateur_id`,`role_id`),
  ADD KEY `role_id` (`role_id`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `annees_academiques`
--
ALTER TABLE `annees_academiques`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT pour la table `calendrier_paiement`
--
ALTER TABLE `calendrier_paiement`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT pour la table `calendrier_tranches`
--
ALTER TABLE `calendrier_tranches`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT pour la table `classes`
--
ALTER TABLE `classes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT pour la table `eleves`
--
ALTER TABLE `eleves`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT pour la table `frais`
--
ALTER TABLE `frais`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT pour la table `inscriptions`
--
ALTER TABLE `inscriptions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT pour la table `logs`
--
ALTER TABLE `logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT pour la table `options`
--
ALTER TABLE `options`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT pour la table `paiements`
--
ALTER TABLE `paiements`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT pour la table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT pour la table `semestres`
--
ALTER TABLE `semestres`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT pour la table `types_frais`
--
ALTER TABLE `types_frais`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT pour la table `utilisateurs`
--
ALTER TABLE `utilisateurs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `calendrier_paiement`
--
ALTER TABLE `calendrier_paiement`
  ADD CONSTRAINT `calendrier_paiement_ibfk_1` FOREIGN KEY (`annee_id`) REFERENCES `annees_academiques` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `calendrier_tranches`
--
ALTER TABLE `calendrier_tranches`
  ADD CONSTRAINT `calendrier_tranches_ibfk_1` FOREIGN KEY (`calendrier_id`) REFERENCES `calendrier_paiement` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `classes`
--
ALTER TABLE `classes`
  ADD CONSTRAINT `classes_ibfk_1` FOREIGN KEY (`option_id`) REFERENCES `options` (`id`);

--
-- Contraintes pour la table `frais`
--
ALTER TABLE `frais`
  ADD CONSTRAINT `frais_ibfk_1` FOREIGN KEY (`type_frais_id`) REFERENCES `types_frais` (`id`),
  ADD CONSTRAINT `frais_ibfk_2` FOREIGN KEY (`classe_id`) REFERENCES `classes` (`id`),
  ADD CONSTRAINT `frais_ibfk_3` FOREIGN KEY (`semestre_id`) REFERENCES `semestres` (`id`),
  ADD CONSTRAINT `frais_ibfk_4` FOREIGN KEY (`calendrier_id`) REFERENCES `calendrier_paiement` (`id`) ON DELETE SET NULL;

--
-- Contraintes pour la table `inscriptions`
--
ALTER TABLE `inscriptions`
  ADD CONSTRAINT `inscriptions_ibfk_1` FOREIGN KEY (`eleve_id`) REFERENCES `eleves` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `inscriptions_ibfk_2` FOREIGN KEY (`classe_id`) REFERENCES `classes` (`id`),
  ADD CONSTRAINT `inscriptions_ibfk_3` FOREIGN KEY (`annee_id`) REFERENCES `annees_academiques` (`id`);

--
-- Contraintes pour la table `logs`
--
ALTER TABLE `logs`
  ADD CONSTRAINT `logs_ibfk_1` FOREIGN KEY (`utilisateur_id`) REFERENCES `utilisateurs` (`id`) ON DELETE SET NULL;

--
-- Contraintes pour la table `paiements`
--
ALTER TABLE `paiements`
  ADD CONSTRAINT `paiements_ibfk_1` FOREIGN KEY (`inscription_id`) REFERENCES `inscriptions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `paiements_ibfk_2` FOREIGN KEY (`frais_id`) REFERENCES `frais` (`id`),
  ADD CONSTRAINT `paiements_ibfk_3` FOREIGN KEY (`created_by`) REFERENCES `utilisateurs` (`id`) ON DELETE SET NULL;

--
-- Contraintes pour la table `semestres`
--
ALTER TABLE `semestres`
  ADD CONSTRAINT `semestres_ibfk_1` FOREIGN KEY (`annee_id`) REFERENCES `annees_academiques` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `utilisateur_roles`
--
ALTER TABLE `utilisateur_roles`
  ADD CONSTRAINT `utilisateur_roles_ibfk_1` FOREIGN KEY (`utilisateur_id`) REFERENCES `utilisateurs` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `utilisateur_roles_ibfk_2` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
