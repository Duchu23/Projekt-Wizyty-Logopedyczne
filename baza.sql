-- ============================================================
--  GABINET LOGOPEDYCZNY – baza danych
--  Plik do zaimportowania w phpMyAdmin
--  (phpMyAdmin -> Import -> wybierz ten plik -> Wykonaj)
-- ============================================================

-- 1. Tworzymy bazę (jeśli jeszcze nie istnieje) z polskimi znakami
CREATE DATABASE IF NOT EXISTS gabinet_logopedyczny
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE gabinet_logopedyczny;

-- ------------------------------------------------------------
-- 2. TABELA UŻYTKOWNIKÓW
--    Przechowuje konta rodziców/pacjentów oraz logopedy (admina).
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS uzytkownicy (
  id               INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  login            VARCHAR(50)  NOT NULL UNIQUE,   -- unikalna nazwa logowania
  haslo            VARCHAR(255) NOT NULL,          -- hash z password_hash() (~60 znaków)
  email            VARCHAR(100) NOT NULL,
  is_admin         TINYINT(1)   NOT NULL DEFAULT 0, -- 0 = rodzic, 1 = logopeda (admin)
  data_rejestracji TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ------------------------------------------------------------
-- 3. TABELA SPECJALISTÓW
--    Logopedzi przyjmujący w gabinecie. Do wyboru przy umawianiu wizyty.
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS specjalisci (
  id            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  imie_nazwisko VARCHAR(120) NOT NULL,
  tytul         VARCHAR(60),                       -- np. "mgr logopedii"
  specjalizacja VARCHAR(120),                      -- np. "logopedia dziecięca"
  opis          TEXT
);

-- ------------------------------------------------------------
-- 4. TABELA WIZYT (rezerwacje)
--    Każda wizyta należy do jednego użytkownika i jednego specjalisty.
--    UNIQUE pilnuje, by jeden specjalista nie miał dwóch wizyt
--    o tej samej dacie i godzinie (brak podwójnych rezerwacji).
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS wizyty (
  id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  id_uzytkownika  INT UNSIGNED NOT NULL,
  id_specjalisty  INT UNSIGNED NOT NULL,
  data_wizyty     DATE NOT NULL,
  godzina         TIME NOT NULL,
  cel             TEXT,                             -- powód/opis wizyty
  data_utworzenia TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  INDEX (id_uzytkownika),
  INDEX (id_specjalisty),
  UNIQUE KEY jeden_termin (id_specjalisty, data_wizyty, godzina),
  FOREIGN KEY (id_uzytkownika) REFERENCES uzytkownicy(id) ON DELETE CASCADE ON UPDATE CASCADE,
  FOREIGN KEY (id_specjalisty) REFERENCES specjalisci(id) ON DELETE CASCADE ON UPDATE CASCADE
);

-- ------------------------------------------------------------
-- 5. TABELA KATEGORII
--    Grupy ćwiczeń (np. głoska R, ćwiczenia oddechowe...).
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS kategorie (
  id    INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  nazwa VARCHAR(60) NOT NULL,
  opis  TEXT
);

-- ------------------------------------------------------------
-- 6. TABELA ĆWICZEŃ
--    Każde ćwiczenie należy do dokładnie jednej kategorii.
--    id_kategorii to KLUCZ OBCY wskazujący na kategorie(id).
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS cwiczenia (
  id           INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  id_kategorii INT UNSIGNED NOT NULL,
  tytul        VARCHAR(120) NOT NULL,
  autor        VARCHAR(120) NOT NULL,
  poziom       TINYINT      NOT NULL,
  wiek         VARCHAR(30)  DEFAULT NULL,
  zdjecie      VARCHAR(100) DEFAULT NULL,
  opis         TEXT,
  INDEX (id_kategorii),
  FOREIGN KEY (id_kategorii) REFERENCES kategorie(id)
    ON DELETE CASCADE ON UPDATE CASCADE
);

-- ------------------------------------------------------------
-- 7. TABELA KOMENTARZY
--    Każde ćwiczenie może mieć wiele komentarzy (relacja 1:wiele).
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS komentarze (
  id           INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  id_cwiczenia INT UNSIGNED NOT NULL,
  nick         VARCHAR(50)  NOT NULL,
  ocena        INT          NOT NULL,
  tresc        TEXT,
  data_dodania TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  INDEX (id_cwiczenia),
  FOREIGN KEY (id_cwiczenia) REFERENCES cwiczenia(id)
    ON DELETE CASCADE ON UPDATE CASCADE
);

-- ------------------------------------------------------------
-- 8. TABELA ULUBIONYCH (polubienia ćwiczeń)
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS ulubione (
  id             INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  id_cwiczenia   INT UNSIGNED NOT NULL,
  id_uzytkownika INT UNSIGNED NOT NULL,
  INDEX (id_cwiczenia),
  INDEX (id_uzytkownika),
  UNIQUE KEY unikalne_polubienie (id_cwiczenia, id_uzytkownika),
  FOREIGN KEY (id_cwiczenia)   REFERENCES cwiczenia(id)   ON DELETE CASCADE ON UPDATE CASCADE,
  FOREIGN KEY (id_uzytkownika) REFERENCES uzytkownicy(id) ON DELETE CASCADE ON UPDATE CASCADE
);

-- ============================================================
--  DANE STARTOWE
-- ============================================================

INSERT INTO specjalisci (imie_nazwisko, tytul, specjalizacja, opis) VALUES
('Anna Kowalska',      'mgr logopedii',          'logopedia dziecięca',  'Specjalizuje się w terapii wad wymowy u dzieci w wieku przedszkolnym i wczesnoszkolnym.'),
('Piotr Nowak',        'mgr logopedii',          'terapia oddechowa',    'Prowadzi ćwiczenia oddechowe i terapię jąkania.'),
('Katarzyna Wiśniewska','dr n. hum., neurologopeda','neurologopedia',    'Zajmuje się terapią po urazach oraz opóźnionym rozwojem mowy.');

INSERT INTO kategorie (nazwa, opis) VALUES
('Głoska R',            'Ćwiczenia przygotowujące i utrwalające prawidłową wymowę głoski R'),
('Głoski szumiące',     'Ćwiczenia na głoski SZ, Ż, CZ, DŻ'),
('Głoski syczące',      'Ćwiczenia na głoski S, Z, C, DZ'),
('Ćwiczenia oddechowe', 'Ćwiczenia wspierające prawidłowy tor oddechowy i wydłużanie fazy wydechu'),
('Motoryka narządów mowy', 'Ćwiczenia języka, warg i żuchwy'),
('Słuch fonematyczny',  'Ćwiczenia rozróżniania i analizy dźwięków mowy');

INSERT INTO cwiczenia (id_kategorii, tytul, autor, poziom, wiek, zdjecie, opis) VALUES
(1, 'Konik – kląskanie językiem', 'mgr Anna Kowalska', 2, '4-7 lat', NULL,
 'Dziecko naśladuje stukot kopyt konia, energicznie kląskając językiem o podniebienie. Ćwiczenie pionizuje język i przygotowuje go do wymowy głoski R.'),
(1, 'Malarz – malowanie podniebienia', 'mgr Anna Kowalska', 1, '3-6 lat', NULL,
 'Czubek języka „maluje" podniebienie od zębów w stronę gardła. Wzmacnia mięśnie języka i poprawia jego ruchomość.'),
(2, 'Wąż – przeciągłe SZ', 'mgr Anna Kowalska', 2, '4-8 lat', NULL,
 'Dziecko naśladuje syk węża, długo wymawiając głoskę SZ z zaokrąglonymi wargami. Utrwala prawidłowy układ narządów mowy.'),
(4, 'Dmuchanie na wiatraczek', 'mgr Piotr Nowak', 1, '3-6 lat', NULL,
 'Dziecko dmucha na papierowy wiatraczek tak, aby się obracał. Ćwiczy długi, równomierny wydech – podstawę prawidłowej mowy.'),
(5, 'Wesołe minki przed lustrem', 'mgr Piotr Nowak', 1, '3-7 lat', NULL,
 'Dziecko przed lustrem naśladuje miny: uśmiech, dziubek, nadymanie policzków. Rozwija sprawność warg i mięśni twarzy.');

INSERT INTO komentarze (id_cwiczenia, nick, ocena, tresc) VALUES
(1, 'rodzic_oli',  5, 'Córka uwielbia to ćwiczenie, robimy je codziennie!'),
(1, 'tata_kuby',   4, 'Działa, choć na początku było trudno.'),
(4, 'mama_zosi',   5, 'Świetna zabawa, a przy okazji ćwiczenie.');
