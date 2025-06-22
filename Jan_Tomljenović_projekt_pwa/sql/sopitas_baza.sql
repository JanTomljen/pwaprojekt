-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 21, 2025 at 06:44 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `sopitas_baza`
--

-- --------------------------------------------------------

--
-- Table structure for table `korisnik`
--

CREATE TABLE `korisnik` (
  `id` int(11) NOT NULL,
  `ime` varchar(32) NOT NULL,
  `prezime` varchar(32) NOT NULL,
  `korisnicko_ime` varchar(32) NOT NULL,
  `lozinka` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `korisnik`
--

INSERT INTO `korisnik` (`id`, `ime`, `prezime`, `korisnicko_ime`, `lozinka`) VALUES
(1, 'Admin', 'User', 'admin', '$2y$10$P/k0ftb4TOd/QYiArtDkHeafGHXJ4Hg.9oJa6l.DsLdVA7KUrRP1S'),
(2, 'da', 'da', 'da', '$2y$10$z0d0pZ0wOoh5xld15ESBneQG4OFn05BFTXz1vect9VZY7bGHPXrhe'),
(3, 'Jan ', 'Tomljenović', 'JanTomljen', '$2y$10$1VAR4Z2BPAW.3wmEGIo8QuPoK6EQQhxNNsbVGW29zQFYgGJBCRpby'),
(4, 'Jan', 'Tomljen', 'JanT', '$2y$10$yJTVmkTxH3v7p6jc6Hn3ZOOECj4CldoNRtMuxhEsbLCQJ1P9x0xl.');

-- --------------------------------------------------------

--
-- Table structure for table `vijesti`
--

CREATE TABLE `vijesti` (
  `id` int(11) NOT NULL,
  `datum` datetime NOT NULL DEFAULT current_timestamp(),
  `naslov` varchar(255) NOT NULL,
  `sazetak` varchar(100) NOT NULL,
  `tekst` text NOT NULL,
  `slika` varchar(255) NOT NULL,
  `kategorija` varchar(50) NOT NULL,
  `arhiva` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `vijesti`
--

INSERT INTO `vijesti` (`id`, `datum`, `naslov`, `sazetak`, `tekst`, `slika`, `kategorija`, `arhiva`) VALUES
(18, '2025-06-21 06:33:19', 'Chelsea je osvojio UEFA konferencijsku ligu ', 'CHELSEA je pobijedio Real Betis 4:1 i osvojio Konferencijsku ligu.', 'Englezi su postali četvrti osvajači ovog natjecanja nakon Rome, West Hama i Olympiakosa te su postali prvi u povijesti koji su osvojili Ligu prvaka, Europsku ligu, Konferencijsku ligu i Kup pobjednika kupova.\r\n\r\nNije to dobro izgledalo za Chelsea na početku utakmice, kada je Betis dominirao i zabio već u 9. minuti, strijelac je bio Ezzalzouli. Nastavio je Betis s dominacijom, bili su blizu povećanja vodstva u nekoliko navrata, ali nisu uspjeli zabiti drugi pogodak. Chelsea u prvom poluvremenu nije pokazao gotovo ništa i činilo se da će Španjolci bez problema doći do trofeja.\r\n\r\nDrugo poluvrijeme druga krajnost naspram prvog\r\nU drugom poluvremenu stvari su se bitno promijenilo. Velik udarac zadobio je Betis u 53. minuti kada je Ezzalzouli, do tada najbolji igrač na terenu, morao izaći iz igre zbog ozljede i nakon toga više gotovo da nisu postojali na terenu. U 65. minuti Palmer je sjajno asistirao s desnog krila, a Fernandez glavom pospremio loptu iza Adriana za izjednačenje.\r\n\r\nTaj pogodak presjekao je Betis i od tada se nisu uspjeli vratiti. Samo pet minuta nakon izjednačenja Chelsea je zabio, Jackson je na asistenciju Palmera pogodio za 2:1. Od tada Španjolci nisu uspjeli ništa napraviti, a Chelsea je nastavio stvarati prilike iz kontri.\r\n\r\nU 83. minuti Sancho je zabio za 3:1 niza neobjašnjivih reakcija igrača Betisa, loše obrambene reakcije kumovale su tome da Sancho dobije loptu na lijevoj strani i pogodi suprotni kut za 3:1. Od Betisa do kraja treba istaknuti samo to da je Antony izgubio živce nakon prekršaja na Fernandezu te izazvao sukob na terenu. Konačnih 4:1 nakon kontre je zabio Caicedo za veliko slavlje i trofej londonskog kluba.', 'chelsea.jpg', 'sport', 0),
(19, '2025-06-21 06:36:36', 'Emotivni Antony oprostio se od Real Betisa: Vratili ste mi radost igranja nogometa', 'Brazilski nogometaš Antony objavio je da završava svoju epizodu u Real Betisu.', 'Putem društvenih mreža potvrdio je kako do produženja ugovora neće doći, čime je i službeno najavio svoj odlazak iz španjolskog kluba.\r\n\r\n“Hvala puno za ovu čarobnu sezonu, Betis. Moji ljudi. Moj svijet. Jedno od najljepših poglavlja u mom životu bliži se kraju. S Betisom sam pronašao osmijeh. Zauvijek”, poručio je Antony koji se jako brzo uvukao pod kožu navijačima seviljskog kluba.\r\n\r\nKako i ne bi, s obzirom na to da je u Real Betisu pružao sjajne partije. U samo 26 odigranih utakmica postigao je devet golova i upisao pet asistencija. Za usporedbu, u dresu Manchester Uniteda zabio je 12 golova i dodao pet asistencija u čak 96 nastupa. U Betisu se Brazilac očito preporodio i napokon pokazao zbog čega je United svojedobno platio čak 95 milijuna eura za njegov transfer.\r\n\r\n“Od prvog koraka u ovaj klub osjetio sam nešto drugačije. Bilo je to kao da sam se vratio kući, kao da sam pronašao dio sebe za koji sam mislio da sam ga izgubio. S vam sam se opet počeo smijati. Hvala vam što ste me primili kao jednog od svojih. Hvala vam što ste mi vratili radost igranja nogometa. I hvala vam što ste me podsjetili zašto sam se zaljubio u ovu igru”, poručio je emotivni Antony.\r\n\r\nSada će se vratiti u matični Manchester United s kojim ima ugovor do 2027., no tek treba vidjeti računa li Rúben Amorim na njega', 'sport1.jpg', 'sport', 0);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `korisnik`
--
ALTER TABLE `korisnik`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `korisnicko_ime` (`korisnicko_ime`);

--
-- Indexes for table `vijesti`
--
ALTER TABLE `vijesti`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `korisnik`
--
ALTER TABLE `korisnik`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `vijesti`
--
ALTER TABLE `vijesti`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
