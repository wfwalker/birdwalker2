use birdwalker

--
-- Table structure for table 'location'
--

drop table if exists location;

CREATE TABLE location (
  objectid mediumint(9) NOT NULL auto_increment,
  Name varchar(255),
  ReferenceURL text,
  City text,
  County varchar(255),
  State char(2),
  Notes text,
  LatLongSystem text,
  Latitude text,
  Longitude text,
  PRIMARY KEY  (objectid),
  KEY NameIndex (Name),
  KEY CountyIndex (County),
  KEY StateIndex (State)
) TYPE=MyISAM;

--
-- Table structure for table 'species'
--

drop table if exists species;

CREATE TABLE species (
  objectid bigint(20) NOT NULL default '0',
  Abbreviation varchar(16) default NULL,
  LatinName text,
  CommonName text,
  Notes text,
  ReferenceURL text,
  ABACountable int(2) NOT NULL default '1',
  PRIMARY KEY  (objectid),
  KEY AbbreviationIndex (Abbreviation),
  KEY ABACountableIndex (ABACountable)
) TYPE=MyISAM;

--
-- Table structure for table 'taxonomy'
--

drop table if exists taxonomy;

CREATE TABLE taxonomy (
  objectid bigint(20) NOT NULL default '0',
  HierarchyLevel varchar(16) default NULL,
  LatinName text,
  CommonName text,
  Notes text,
  ReferenceURL text,
  PRIMARY KEY  (objectid),
  KEY objectidIndex (objectid),
  KEY hierarchyLevelIndex (HierarchyLevel)
) TYPE=MyISAM;

--
-- Table structure for table 'trip'
--

drop table if exists trip;

CREATE TABLE trip (
  objectid mediumint(9) NOT NULL auto_increment,
  Leader text,
  ReferenceURL text,
  Name text,
  Notes text,
  Date date default NULL,
  PRIMARY KEY  (objectid),
  KEY dateIndex (date)
) TYPE=MyISAM;

--
-- Table structure for table 'sighting'
--

drop table if exists sighting;

CREATE TABLE sighting (
  objectid mediumint(9) NOT NULL auto_increment,
  SpeciesAbbreviation varchar(16) default NULL,
  LocationName varchar(255),
  Notes text,
  Exclude varchar(16) default NULL,
  Photo varchar(16) default NULL,
  TripDate date default NULL,
  PRIMARY KEY  (objectid),
  KEY SpeciesAbbreviationIndex (SpeciesAbbreviation),
  KEY TripDateIndex (TripDate),
  KEY LocationNameIndex (LocationName)
) TYPE=MyISAM;


--
-- Table structure for table 'state'
--

drop table if exists state;

CREATE TABLE state (
  objectid mediumint(9) NOT NULL auto_increment,
  Name varchar(16) default NULL,
  Abbreviation varchar(16) default NULL,
  Notes text,
  PRIMARY KEY  (objectid)
) TYPE=MyISAM;
