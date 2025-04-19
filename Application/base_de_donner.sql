create table categorie (
    id_cat int primary key autoincrement,
    nom_cat varchar(50) not null,
);

create table users (
    id_user int primary key autoincrement,
    nom_user varchar(50) not null,
    prenom_user varchar(50) not null,
    email_user varchar(50) not null,
    mot_de_passe_user varchar(50) not null,
    photo_user BLOB not null -- Utiliser BLOB pour stocker des images
);
 create table depense (
    id_depense int primary key autoincrement,
    id_user int not null,
    id_cat int not null,
    date_depense date not null,
    montant_depense int not null,
    description_depense varchar(50) not null,
    foreign key (id_user) references users(id_user),
    foreign key (id_cat) references categorie(id_cat)
);

create table revenue (
    id_revenu int primary key autoincrement,
    id_user int not null,
    date_revenu date not null,
    montant_revenu int not null,
    description_revenu varchar(50) not null,
    foreign key (id_user) references users(id_user)
);