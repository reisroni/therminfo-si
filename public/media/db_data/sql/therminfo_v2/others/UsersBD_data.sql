--
-- Database: therminfo2
--

USE therminfo2;
TRUNCATE user;

-- -----------------------------------------------------
-- (1) user data (sem encriptacao)
-- -----------------------------------------------------
INSERT INTO user (uid, u_first_name, u_last_name, email, institution, password, type, validated, outdated) VALUES
(1, 'Ana', 'Teixeira', 'analinoteixeira@gmail.com', 'FCUL', 'black', 'superadmin', 1, 0),
(2, 'Rui', 'Santos', 'rjcs@fc.ul.pt', 'FCUL', 'tezamyras', 'admin', 1, 0),
(3, 'Ana', 'Teixeira', 'ateixeira@lasige.di.fc.ul.pt', 'FCUL', 'edemanene', 'guest', 1, 0),
(4, 'Andre', 'Falcao', 'afalcao@di.fc.ul.pt', 'DI-FCUL', 'xpto', 'guest', 1, 0),
(5, 'ThermInfo', 'TI', 'therminfo@gmail.com', 'FCUL', 'ahenyryde', 'guest', 1, 0),
(6, 'Rony', 'Reis', 'reisrony@gmail.com', 'FCUL - Lasige', 'wilsonreis', 'superadmin', 1, 0),
(7, 'Kari I.', 'Keskinen', 'kari.keskinen@tkk.fi', 'Aalto University, School of Chemical Technology', 'hepymeten', 'guest', 1, 0),
(8, 'Jose Artur', 'Martinho Simoes', 'jams@fc.ul.pt', 'FCUL', 'naguzanun', 'guest', 1, 0),
(9, 'ALBERT', 'VAM', 'avcheme@gmail.com', 'university of dayton research institute', 'gudyrymad', 'guest', 1, 0)
(10, 'Teste', 'T', 'teste@teste.com', 'Teste', 'teste', 'admin', 1, 0);