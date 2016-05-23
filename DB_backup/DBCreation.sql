-- --------------------------------------------------
-- Creating all tables
-- --------------------------------------------------

CREATE TABLE NoProfilePic(
	FakePhotoId bigint NOT NULL);

ALTER TABLE NoProfilePic ADD PRIMARY KEY (FakePhotoId);

CREATE TABLE PhotoAttributes(
	Id bigint NOT NULL AUTO_INCREMENT UNIQUE, 
	PhotoId bigint NOT NULL, 
	Gender bool NOT NULL, 
	EyeColor TINYINT NOT NULL, 
	HairColor TINYINT NOT NULL, 
	HasBeard bool NOT NULL, 
	HasGlasses bool NOT NULL, 
	HasSmile bool NOT NULL, 
	Age int NOT NULL, 
	UpdateDate datetime NOT NULL, 
	UpdatedByUser bool NOT NULL);

ALTER TABLE PhotoAttributes ADD PRIMARY KEY (Id);

CREATE TABLE Photos(
	Id bigint NOT NULL AUTO_INCREMENT UNIQUE, 
	FacebookPhotoId bigint NOT NULL, 
	FacebookId bigint NOT NULL, 
	UpdateDate datetime NOT NULL, 
	PhotoLink varchar (255) NOT NULL, 
	NumOfLikes int NOT NULL, 
	IsValidPhoto bool NOT NULL);

ALTER TABLE Photos ADD PRIMARY KEY (Id);

CREATE TABLE Users(
	FacebookId bigint NOT NULL, 
	FirstName varchar (20) NOT NULL, 
	LastName varchar (20) NOT NULL);

ALTER TABLE Users ADD PRIMARY KEY (FacebookId);

CREATE TABLE History(
	Id bigint NOT NULL AUTO_INCREMENT UNIQUE, 
	FacebookId bigint NOT NULL, 
	AttributeName longtext NOT NULL, 
	FilterValue longtext NOT NULL, 
	SessionId longtext NOT NULL);

ALTER TABLE History ADD PRIMARY KEY (Id);


CREATE TABLE PhotoComments(
	Id int NOT NULL AUTO_INCREMENT UNIQUE, 
	Comment longtext NOT NULL, 
	PhotoId bigint NOT NULL, 
	FacebookId bigint NOT NULL, 
	Time datetime NOT NULL);

ALTER TABLE PhotoComments ADD PRIMARY KEY (Id);


CREATE TABLE PhotoRatings(
	Id int NOT NULL AUTO_INCREMENT UNIQUE, 
	IsHot bool NOT NULL, 
	PhotosId bigint NOT NULL, 
	FacebookId bigint NOT NULL);

ALTER TABLE PhotoRatings ADD PRIMARY KEY (Id);

-- --------------------------------------------------
-- Creating all FOREIGN KEY constraints
-- --------------------------------------------------


-- Creating foreign key on PhotoId in table 'PhotoAttributes'

ALTER TABLE PhotoAttributes
ADD CONSTRAINT FK_Photos_To_Attributes
    FOREIGN KEY (PhotoId)
    REFERENCES Photos
        (Id)
    ON DELETE NO ACTION ON UPDATE NO ACTION;


-- Creating non-clustered index for FOREIGN KEY 'FK_Photos_To_Attributes'

CREATE INDEX IX_FK_Photos_To_Attributes
    ON PhotoAttributes
    (PhotoId);



-- Creating foreign key on FacebookId in table 'Photos'

ALTER TABLE Photos
ADD CONSTRAINT FK_Users_To_Photos
    FOREIGN KEY (FacebookId)
    REFERENCES Users
        (FacebookId)
    ON DELETE NO ACTION ON UPDATE NO ACTION;


-- Creating non-clustered index for FOREIGN KEY 'FK_Users_To_Photos'

CREATE INDEX IX_FK_Users_To_Photos
    ON Photos
    (FacebookId);



-- Creating foreign key on FacebookId in table 'History'

ALTER TABLE History
ADD CONSTRAINT FK_UsersHistory
    FOREIGN KEY (FacebookId)
    REFERENCES Users
        (FacebookId)
    ON DELETE NO ACTION ON UPDATE NO ACTION;


-- Creating non-clustered index for FOREIGN KEY 'FK_UsersHistory'

CREATE INDEX IX_FK_UsersHistory
    ON History
    (FacebookId);



-- Creating foreign key on PhotoId in table 'PhotoComments'

ALTER TABLE PhotoComments
ADD CONSTRAINT FK_Photos_To_PhotoComment
    FOREIGN KEY (PhotoId)
    REFERENCES Photos
        (Id)
    ON DELETE NO ACTION ON UPDATE NO ACTION;


-- Creating non-clustered index for FOREIGN KEY 'FK_Photos_To_PhotoComment'

CREATE INDEX IX_FK_Photos_To_PhotoComment
    ON PhotoComments
    (PhotoId);



-- Creating foreign key on FacebookId in table 'PhotoComments'

ALTER TABLE PhotoComments
ADD CONSTRAINT FK_Users_To_PhotoComment
    FOREIGN KEY (FacebookId)
    REFERENCES Users
        (FacebookId)
    ON DELETE NO ACTION ON UPDATE NO ACTION;


-- Creating non-clustered index for FOREIGN KEY 'FK_Users_To_PhotoComment'

CREATE INDEX IX_FK_Users_To_PhotoComment
    ON PhotoComments
    (FacebookId);



-- Creating foreign key on PhotosId in table 'PhotoRatings'

ALTER TABLE PhotoRatings
ADD CONSTRAINT FK_Photos_To_PhotoRating
    FOREIGN KEY (PhotosId)
    REFERENCES Photos
        (Id)
    ON DELETE NO ACTION ON UPDATE NO ACTION;


-- Creating non-clustered index for FOREIGN KEY 'FK_Photos_To_PhotoRating'

CREATE INDEX IX_FK_Photos_To_PhotoRating
    ON PhotoRatings
    (PhotosId);



-- Creating foreign key on FacebookId in table 'PhotoRatings'

ALTER TABLE PhotoRatings
ADD CONSTRAINT FK_Users_To_PhotoRating
    FOREIGN KEY (FacebookId)
    REFERENCES Users
        (FacebookId)
    ON DELETE NO ACTION ON UPDATE NO ACTION;


-- Creating non-clustered index for FOREIGN KEY 'FK_Users_To_PhotoRating'

CREATE INDEX IX_FK_Users_To_PhotoRating
    ON PhotoRatings
    (FacebookId);

