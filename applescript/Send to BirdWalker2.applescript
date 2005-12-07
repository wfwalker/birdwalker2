-- starting from a selected item in the main iview catalog,
-- publish it to the local copy of birdwalker2

-- 1. set iview keywords to Bird
-- 2. set the species common namd and birdwalker filename
-- 3. look for a jpeg in the bw2imagesource folder and open photoshop if not
-- 4. make full and small jpegs
-- 5. make sure there's a trip record
-- 6. make sure there's a sighting record

on run
	tell application "iView MediaPro"
		-- make sure only one window open
		if the number of windows < 1 then return
		set theCatalog to catalog 1
		set selectedItems to (selection of theCatalog)
		
		-- look at all the selected items
		repeat with anItem in selectedItems
			-- set the keywords
			if (keywords of annotations of anItem contains "Bird") then
				-- do nothing
			else
				set keywords of annotations of anItem to {"Bird"}
			end if
			
			-- require a species name
			set speciesCommonName to custom field "Species Common Name" of anItem as string
			if (speciesCommonName is "") then
				set UserReply to display dialog "Common Name" default answer "Name"
				set (custom field "Species Common Name" of anItem) to text returned of UserReply
				set speciesCommonName to text returned of UserReply
			end if
			
			-- require a birdwalker filename
			set birdwalkerFilename to custom field "Birdwalker.Filename" of anItem as string
			if (birdwalkerFilename is "") then
				try
					set proposedAbbrev to do shell script "echo \"select Abbreviation from species where CommonName='" & speciesCommonName & "'\" | /usr/local/mysql/bin/mysql --skip-column-names -u birdwalker -pbirdwalker birdwalker"
				on error
					set proposedAbbrev to "XXXXXX"
				end try
				
				set theDate to capture date of anItem
				set proposedFilename to (year of theDate as string) & "-" & (month of theDate as string) & "-" & (day of theDate as string) & "-" & proposedAbbrev
				
				set UserReply to display dialog "Birdwalker.Filename" default answer proposedFilename
				set birdwalkerFilename to text returned of UserReply
				set custom field "Birdwalker.Filename" of anItem to text returned of UserReply
			end if
			
			-- require a location
			set aLocation to location of annotations of anItem as string
			if (aLocation is "") then
				set UserReply to display dialog "location" default answer "2138 King Court"
				set aLocation to text returned of UserReply
				set location of annotations of anItem to text returned of UserReply
			end if
			
			
			set anItemPath to path of anItem
			tell application "Finder"
				if (file (birdwalkerFilename & ".jpg") of folder "bw2imagesource" of folder "Photography Miscellany" of folder "walker" of folder "Users" of startup disk exists) then
					-- do nothing
				else
					set anItemFile to file anItemPath
					open anItemFile using application file "Adobe Photoshop CS2.app" of folder "Adobe Photoshop CS2" of folder "Applications" of startup disk
					
					set the clipboard to birdwalkerFilename
					
					return
				end if
			end tell
			
			tell application "Finder"
				if (file (birdwalkerFilename & ".jpg") of folder "thumb" of folder "images" of folder "birdwalker2" of folder "Sites" of folder "walker" of folder "Users" of startup disk exists) then
					-- do nothing
				else
					display dialog "need to open " & birdwalkerFilename & " in photoshop and run Make BW2"
					set the clipboard to birdwalkerFilename
					return
				end if
				
				if (file (birdwalkerFilename & ".jpg") of folder "photo" of folder "images" of folder "birdwalker2" of folder "Sites" of folder "walker" of folder "Users" of startup disk exists) then
					-- do nothing
				else
					display dialog "need to open " & birdwalkerFilename & " in photoshop and run Make BW2"
					set the clipboard to birdwalkerFilename
					return
				end if
				
			end tell
			
			set tripDate to characters 1 through 10 of birdwalkerFilename as string
			set abbrev to characters 12 through 17 of birdwalkerFilename as string
			
			set tripCount to do shell script "echo \"select count(*) from trip where Date='" & tripDate & "'\" | /usr/local/mysql/bin/mysql --skip-column-names -u birdwalker -pbirdwalker birdwalker"
			
			if (tripCount is equal to "0") then
				set the clipboard to tripDate
				display dialog "About to create trip record for " & tripDate
				do shell script "echo \"insert into trip (Name, Date) values ('Photo Safari', '" & tripDate & "')\" |  /usr/local/mysql/bin/mysql -u birdwalker -pbirdwalker birdwalker"
				set tripID to do shell script "echo \"select objectid from trip where Date='" & tripDate & "'\" | /usr/local/mysql/bin/mysql --skip-column-names -u birdwalker -pbirdwalker birdwalker"
				
				tell application "Firefox"
					OpenURL "http://localhost/~walker/birdwalker2/tripedit.php?tripid=" & tripID
				end tell
				return
			end if
			
			set sightingCount to do shell script "echo \"select count(*) from sighting where TripDate='" & tripDate & "' AND SpeciesAbbreviation='" & abbrev & "'\" | /usr/local/mysql/bin/mysql --skip-column-names -u birdwalker -pbirdwalker birdwalker"
			
			if (sightingCount is equal to "0") then
				display dialog "About to create sighting record for date " & tripDate & " and abbrev " & abbrev
				do shell script "echo \"insert into sighting (TripDate, SpeciesAbbreviation, LocationName) values ('" & tripDate & "', '" & abbrev & "', '" & aLocation & "')\" |  /usr/local/mysql/bin/mysql -u birdwalker -pbirdwalker birdwalker"
			end if
			
			set sightingID to do shell script "echo \"select objectid from sighting where TripDate='" & tripDate & "' and SpeciesAbbreviation='" & abbrev & "'\" | /usr/local/mysql/bin/mysql --skip-column-names -u birdwalker -pbirdwalker birdwalker"
			
			tell application "Firefox"
				OpenURL "http://localhost/~walker/birdwalker2/sightingedit.php?sightingid=" & sightingID
			end tell
			
			display dialog "Please double check sighting for " & (name of anItem) & " as " & birdwalkerFilename
		end repeat
	end tell
end run
