use birdwalker

--
-- Table structure for table 'location'
--

drop table if exists location;

CREATE TABLE location (
  id mediumint(9) NOT NULL auto_increment,
  Name varchar(255),
  ReferenceURL text,
  City text,
  County varchar(255),
  State char(2),
  Notes text,
  LatLongSystem text,
  Latitude float(15,10),
  Longitude float(15,10),
  Photo boolean default 0,
  PRIMARY KEY  (id),
  KEY NameIndex (Name),
  KEY CountyIndex (County),
  KEY StateIndex (State)
) TYPE=MyISAM;

--
-- Table structure for table 'countyfrequency'
--

drop table if exists countyfrequency;

CREATE TABLE countyfrequency (
  CommonName varchar(255),
  Frequency tinyint(2),
  SpeciesID bigint(20)
) TYPE=MyISAM;

--
-- Table structure for table 'species'
--

drop table if exists species;

CREATE TABLE species (
  id bigint(20) NOT NULL default '0',
  Abbreviation varchar(6) default NULL,
  LatinName text,
  CommonName text,
  Notes text,
  ReferenceURL text,
  ABACountable boolean NOT NULL default 1,
  PRIMARY KEY  (id),
  KEY AbbreviationIndex (Abbreviation),
  KEY ABACountableIndex (ABACountable)
) TYPE=MyISAM;

--
-- Table structure for table 'taxonomy'
--

drop table if exists taxonomy;

CREATE TABLE taxonomy (
  id bigint(20) NOT NULL default '0',
  HierarchyLevel varchar(16) default NULL,
  LatinName text,
  CommonName text,
  Notes text,
  ReferenceURL text,
  PRIMARY KEY  (id),
  KEY idIndex (id),
  KEY hierarchyLevelIndex (HierarchyLevel)
) TYPE=MyISAM;

--
-- Table structure for table 'trip'
--

drop table if exists trip;

CREATE TABLE trip (
  id mediumint(9) NOT NULL auto_increment,
  Leader text,
  ReferenceURL text,
  Name text,
  Notes text,
  Date date default NULL,
  PRIMARY KEY  (id),
  KEY dateIndex (date)
) TYPE=MyISAM;

--
-- Table structure for table 'sighting'
--

drop table if exists sighting;

CREATE TABLE `sighting` (
  id mediumint(9) NOT NULL auto_increment,
  Notes text,
  Exclude tinyint(1) default NULL,
  Photo tinyint(1) default NULL,
  location_id mediumint(9) default NULL,
  species_id bigint(9) default NULL,
  trip_id mediumint(9) default NULL,
  PRIMARY KEY  (id),
  KEY ExcludeIndex (Exclude),
  KEY PhotoIndex (Photo),
  KEY LocationIndex (location_id),
  KEY SpeciesIndex (species_id),
  KEY TripIndex (trip_id)
) ENGINE=MyISAM AUTO_INCREMENT=16592 DEFAULT CHARSET=latin1;

--
-- Table structure for table 'state'
--

drop table if exists state;

CREATE TABLE state (
  id mediumint(9) NOT NULL auto_increment,
  Name varchar(16) default NULL,
  Abbreviation varchar(2) default NULL,
  Notes text,
  PRIMARY KEY  (id),
  KEY AbbreviationIndex (Abbreviation)
) TYPE=MyISAM;
