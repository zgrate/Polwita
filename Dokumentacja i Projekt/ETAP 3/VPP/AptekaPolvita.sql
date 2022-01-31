ALTER TABLE Polwita.Reklamacja modify column IdR int (11) NOT NULL;
ALTER TABLE Polwita.Reklamacja DROP PRIMARY KEY;
DROP INDEX IdR ON Polwita.Reklamacja;
DROP INDEX `PRIMARY` ON Polwita.Reklamacja;
ALTER TABLE Reklamacja modify column IdR int (11) NOT NULL;
ALTER TABLE Reklamacja
    ADD PRIMARY KEY (IdR);
ALTER TABLE Reklamacja modify column IdR int (11) NOT NULL AUTO_INCREMENT;
ALTER TABLE Polwita.Dostawa modify column IdD int (11) NOT NULL;
ALTER TABLE Polwita.Dostawa DROP PRIMARY KEY;
DROP INDEX `PRIMARY` ON Polwita.Dostawa;
ALTER TABLE Dostawa modify column IdD int (11) NOT NULL;
ALTER TABLE Dostawa
    ADD PRIMARY KEY (IdD);
ALTER TABLE Dostawa modify column IdD int (11) NOT NULL AUTO_INCREMENT;
ALTER TABLE Reklamacja
    ADD UNIQUE (IdR);
ALTER TABLE Dostawa
    ADD UNIQUE (IdD);
