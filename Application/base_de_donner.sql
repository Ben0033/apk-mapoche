create database apk_mapoche;
use apk_mapoche;
-- Table categorie
create table categorie (
    id_cat int primary key auto_increment,
    nom_cat varchar(50) not null
);

-- Table users
create table users (
    id_user int primary key auto_increment,
    nom_user varchar(50) not null,
    prenom_user varchar(50) not null,
    email_user varchar(50) not null,
    mot_de_passe_user varchar(500) not null, -- Augmentation de la taille pour les mots de passe hashés
    photo_user BLOB not null -- Utiliser BLOB pour stocker des images
);

-- Table depense
create table depense (
    id_depense int primary key auto_increment,
    id_user int not null,
    id_cat int not null,
    date_depense date not null,
    montant_depense int not null,
    description_depense varchar(50) not null,
    foreign key (id_user) references users(id_user),
    foreign key (id_cat) references categorie(id_cat)
);

-- Table revenue
create table revenue (
    id_revenu int primary key auto_increment,
    id_user int not null,
    date_revenu date not null,
    montant_revenu int not null,
    description_revenu varchar(50) not null,
    foreign key (id_user) references users(id_user)
);