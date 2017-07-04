-- phpMyAdmin SQL Dump
-- version 4.7.1
-- https://www.phpmyadmin.net/
--
-- Hôte : localhost:3306
-- Généré le :  mar. 04 juil. 2017 à 10:30
-- Version du serveur :  5.6.35
-- Version de PHP :  5.6.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données :  `gshark`
--

-- --------------------------------------------------------

--
-- Structure de la table `backdoor_list`
--

CREATE TABLE `backdoor_list` (
  `id_backdoor` int(11) NOT NULL,
  `backdoor_identifier` text NOT NULL,
  `backdoor_url` text NOT NULL,
  `created_at` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `gshark_settings`
--

CREATE TABLE `gshark_settings` (
  `id_setting` int(11) NOT NULL,
  `proxy_host` text NOT NULL,
  `proxy_port` text NOT NULL,
  `auto_generate` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Déchargement des données de la table `gshark_settings`
--

INSERT INTO `gshark_settings` (`id_setting`, `proxy_host`, `proxy_port`, `auto_generate`) VALUES
(1, '', '', 'disalow');

-- --------------------------------------------------------

--
-- Structure de la table `session_backdoor`
--

CREATE TABLE `session_backdoor` (
  `id_session` int(11) NOT NULL,
  `backdoor_name` text NOT NULL,
  `current_module` text NOT NULL,
  `module_session` longtext NOT NULL,
  `backdoor_selected` longtext NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Déchargement des données de la table `session_backdoor`
--

INSERT INTO `session_backdoor` (`id_session`, `backdoor_name`, `current_module`, `module_session`, `backdoor_selected`) VALUES
(1, 'master', '', '', 'master');

-- --------------------------------------------------------

--
-- Structure de la table `users_table`
--

CREATE TABLE `users_table` (
  `id_user` int(11) NOT NULL,
  `client_id` text NOT NULL,
  `is_master` int(11) NOT NULL DEFAULT '0',
  `last_asking` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Déchargement des données de la table `users_table`
--

INSERT INTO `users_table` (`id_user`, `client_id`, `is_master`, `last_asking`) VALUES
(1, '113032756', 1, '');

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `backdoor_list`
--
ALTER TABLE `backdoor_list`
  ADD PRIMARY KEY (`id_backdoor`);

--
-- Index pour la table `gshark_settings`
--
ALTER TABLE `gshark_settings`
  ADD PRIMARY KEY (`id_setting`);

--
-- Index pour la table `session_backdoor`
--
ALTER TABLE `session_backdoor`
  ADD PRIMARY KEY (`id_session`);

--
-- Index pour la table `users_table`
--
ALTER TABLE `users_table`
  ADD PRIMARY KEY (`id_user`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `backdoor_list`
--
ALTER TABLE `backdoor_list`
  MODIFY `id_backdoor` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
--
-- AUTO_INCREMENT pour la table `gshark_settings`
--
ALTER TABLE `gshark_settings`
  MODIFY `id_setting` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT pour la table `session_backdoor`
--
ALTER TABLE `session_backdoor`
  MODIFY `id_session` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT pour la table `users_table`
--
ALTER TABLE `users_table`
  MODIFY `id_user` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
