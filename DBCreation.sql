-- Table: PhotoAttributes
CREATE TABLE PhotoAttributes (
    Id bigint NOT NULL AUTO_INCREMENT,
    PhotoId bigint NOT NULL,
    Gender bool NOT NULL,
    EyeColor varchar(6) NOT NULL,
    HairColor varchar(6) NOT NULL,
    HasBeard bool NOT NULL,
    HasGlasses bool NOT NULL,
    HasSmile bool NOT NULL,
    Age int NOT NULL,
    UpdateDate date NOT NULL,
    CONSTRAINT PhotoAttributes_pk PRIMARY KEY (Id)
);

-- Table: NoProfilePic
CREATE TABLE NoProfilePic (
    FakePhotoId bigint NOT NULL,
    CONSTRAINT NoProfilePic_pk PRIMARY KEY (FakePhotoId)
);

-- Table: Photos
CREATE TABLE Photos (
    Id  bigint NOT NULL AUTO_INCREMENT,
    FacebookPhotoId bigint NOT NULL,
    FacebookId bigint NOT NULL,
    UpdateDate date NOT NULL,
    PhotoLink varchar(255) NOT NULL,
    NumOfLikes int NOT NULL,
    IsValidPhoto bool NOT NULL,
    CONSTRAINT Photos_pk PRIMARY KEY (Id)
);

-- Table: Users
CREATE TABLE Users (
    FacebookId bigint NOT NULL,
    FirstName varchar(20) NOT NULL,
    LastName varchar(20) NOT NULL,
    CONSTRAINT Users_pk PRIMARY KEY (FacebookId)
);

-- foreign keys
-- Reference: Photos_To_Attributes (table: PhotoAttributes)
ALTER TABLE PhotoAttributes ADD CONSTRAINT Photos_To_Attributes FOREIGN KEY Photos_To_Attributes (PhotoId)
    REFERENCES Photos (Id);

-- Reference: Users_To_Photos (table: Photos)
ALTER TABLE Photos ADD CONSTRAINT Users_To_Photos FOREIGN KEY Users_To_Photos (FacebookId)
    REFERENCES Users (FacebookId);

-- End of file.
