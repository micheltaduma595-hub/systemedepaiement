
CREATE TABLE IF NOT EXISTS roles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(50) NOT NULL UNIQUE,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT IGNORE INTO roles (nom, description) VALUES
('admin', 'Accès total au système'),
('caissier', 'Gestion des paiements'),
('secretaire', 'Gestion des inscriptions et élèves'),
('comptable', 'Gestion financière');

CREATE TABLE IF NOT EXISTS utilisateurs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    mot_de_passe VARCHAR(255) NOT NULL,
    telephone VARCHAR(20),
    actif TINYINT(1) DEFAULT 1,
    derniere_connexion DATETIME,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS utilisateur_roles (
    utilisateur_id INT NOT NULL,
    role_id INT NOT NULL,
    PRIMARY KEY (utilisateur_id, role_id),
    FOREIGN KEY (utilisateur_id) REFERENCES utilisateurs(id) ON DELETE CASCADE,
    FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS options (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    description TEXT,
    actif TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS classes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(50) NOT NULL,
    niveau VARCHAR(20) NOT NULL,
    option_id INT NOT NULL,
    capacite INT DEFAULT 50,
    actif TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (option_id) REFERENCES options(id) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS annees_academiques (
    id INT AUTO_INCREMENT PRIMARY KEY,
    libelle VARCHAR(20) NOT NULL UNIQUE,
    date_debut DATE NOT NULL,
    date_fin DATE NOT NULL,
    actif TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS semestres (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(50) NOT NULL,
    date_debut DATE NOT NULL,
    date_fin DATE NOT NULL,
    annee_id INT NOT NULL,
    actif TINYINT(1) DEFAULT 1,
    FOREIGN KEY (annee_id) REFERENCES annees_academiques(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS eleves (
    id INT AUTO_INCREMENT PRIMARY KEY,
    matricule VARCHAR(20) NOT NULL UNIQUE,
    nom VARCHAR(100) NOT NULL,
    postnom VARCHAR(100),
    prenom VARCHAR(100) NOT NULL,
    genre ENUM('M', 'F') NOT NULL,
    date_naissance DATE,
    email VARCHAR(100),
    telephone VARCHAR(20),
    adresse TEXT,
    nom_parent VARCHAR(100),
    telephone_parent VARCHAR(20),
    email_parent VARCHAR(100),
    actif TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS inscriptions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    eleve_id INT NOT NULL,
    classe_id INT NOT NULL,
    annee_id INT NOT NULL,
    date_inscription DATE NOT NULL,
    statut ENUM('actif', 'abandonne', 'diplome', 'exclu') DEFAULT 'actif',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (eleve_id) REFERENCES eleves(id) ON DELETE CASCADE,
    FOREIGN KEY (classe_id) REFERENCES classes(id) ON DELETE RESTRICT,
    FOREIGN KEY (annee_id) REFERENCES annees_academiques(id) ON DELETE RESTRICT,
    UNIQUE KEY unique_inscription (eleve_id, annee_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS types_frais (
    id INT AUTO_INCREMENT PRIMARY KEY,
    libelle VARCHAR(100) NOT NULL,
    description TEXT,
    actif TINYINT(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT IGNORE INTO types_frais (libelle, description) VALUES
('Minerval', 'Frais de scolarité principal'),
('Inscription', 'Frais d\'inscription'),
('Réinscription', 'Frais de réinscription'),
('Examen', 'Frais d\'examen'),
('Bibliothèque', 'Frais de bibliothèque'),
('Assurance', 'Assurance scolaire'),
('Transport', 'Frais de transport');

CREATE TABLE IF NOT EXISTS calendrier_paiement (
    id INT AUTO_INCREMENT PRIMARY KEY,
    libelle VARCHAR(100) NOT NULL,
    description TEXT,
    date_debut DATE NOT NULL,
    date_fin DATE NOT NULL,
    annee_id INT NOT NULL,
    actif TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (annee_id) REFERENCES annees_academiques(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS frais (
    id INT AUTO_INCREMENT PRIMARY KEY,
    type_frais_id INT NOT NULL,
    classe_id INT NOT NULL,
    semestre_id INT NOT NULL,
    calendrier_id INT,
    montant DECIMAL(10,2) NOT NULL,
    libelle VARCHAR(200) NOT NULL,
    date_limite DATE,
    statut ENUM('actif', 'inactif') DEFAULT 'actif',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (type_frais_id) REFERENCES types_frais(id),
    FOREIGN KEY (classe_id) REFERENCES classes(id),
    FOREIGN KEY (semestre_id) REFERENCES semestres(id),
    FOREIGN KEY (calendrier_id) REFERENCES calendrier_paiement(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS paiements (
    id INT AUTO_INCREMENT PRIMARY KEY,
    inscription_id INT NOT NULL,
    frais_id INT NOT NULL,
    montant_paye DECIMAL(10,2) NOT NULL,
    date_paiement DATETIME NOT NULL,
    mode_paiement ENUM('espece', 'mobile_money', 'virement', 'cheque', 'carte') NOT NULL,
    reference VARCHAR(100),
    statut ENUM('en_attente', 'valide', 'annule', 'rembourse') DEFAULT 'valide',
    commentaire TEXT,
    recu_numero VARCHAR(50),
    created_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (inscription_id) REFERENCES inscriptions(id) ON DELETE CASCADE,
    FOREIGN KEY (frais_id) REFERENCES frais(id),
    FOREIGN KEY (created_by) REFERENCES utilisateurs(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS notifications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    type ENUM('paiement', 'rappel', 'echeance', 'information') NOT NULL,
    destinataire_email VARCHAR(100) NOT NULL,
    sujet VARCHAR(200) NOT NULL,
    message TEXT NOT NULL,
    envoye TINYINT(1) DEFAULT 0,
    date_envoi DATETIME,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
