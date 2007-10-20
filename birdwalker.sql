use birdwalker

--
-- Table structure for table 'location'
--

drop table if exists locations;

CREATE TABLE locations (
  id mediumint(9) NOT NULL auto_increment,
  name varchar(255),
  reference_url text,
  city text,
  county varchar(255),
  state char(2),
  notes text,
  latitude float(15,10),
  longitude float(15,10),
  photo boolean default 0,
  PRIMARY KEY  (id),
  KEY NameIndex (name),
  KEY CountyIndex (county),
  KEY StateIndex (state)
) TYPE=MyISAM;

--
-- Table structure for table 'countyfrequency'
--

drop table if exists countyfrequency;

CREATE TABLE countyfrequency (
  common_name varchar(255),
  frequency tinyint(2),
  species_id bigint(20)
) TYPE=MyISAM;

--
-- Table structure for table 'species'
--

drop table if exists species;

CREATE TABLE species (
  id bigint(20) NOT NULL default '0',
  abbreviation varchar(6) default NULL,
  latin_name text,
  common_name text,
  notes text,
  reference_url text,
  aba_countable boolean NOT NULL default 1,
  PRIMARY KEY  (id),
  KEY AbbreviationIndex (abbreviation),
  KEY aba_countableIndex (aba_countable)
) TYPE=MyISAM;

--
-- Table structure for table 'taxonomy'
--

drop table if exists taxonomy;

CREATE TABLE taxonomy (
  id bigint(20) NOT NULL default '0',
  hierarchy_level varchar(16) default NULL,
  latin_name text,
  common_name text,
  notes text,
  reference_url text,
  PRIMARY KEY  (id),
  KEY idIndex (id),
  KEY hierarchyLevelIndex (hierarchy_level)
) TYPE=MyISAM;

--
-- Table structure for table 'trip'
--

drop table if exists trips;

CREATE TABLE trips (
  id mediumint(9) NOT NULL auto_increment,
  leader text,
  reference_url text,
  name text,
  notes text,
  date date default NULL,
  PRIMARY KEY  (id),
  KEY dateIndex (date)
) TYPE=MyISAM;

--
-- Table structure for table 'sighting'
--

drop table if exists sightings;

CREATE TABLE `sightings` (
  id mediumint(9) NOT NULL auto_increment,
  notes text,
  exclude tinyint(1) default NULL,
  photo tinyint(1) default NULL,
  location_id mediumint(9) default NULL,
  species_id bigint(9) default NULL,
  trip_id mediumint(9) default NULL,
  PRIMARY KEY  (id),
  KEY ExcludeIndex (exclude),
  KEY PhotoIndex (photo),
  KEY LocationIndex (location_id),
  KEY SpeciesIndex (species_id),
  KEY TripIndex (trip_id)
) ENGINE=MyISAM AUTO_INCREMENT=16592 DEFAULT CHARSET=latin1;

--
-- Table structure for table 'state'
--

drop table if exists states;

CREATE TABLE states (
  id mediumint(9) NOT NULL auto_increment,
  name varchar(16) default NULL,
  abbreviation varchar(2) default NULL,
  notes text,
  PRIMARY KEY  (id),
  KEY AbbreviationIndex (abbreviation)
) TYPE=MyISAM;
