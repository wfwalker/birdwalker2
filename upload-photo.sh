ssh walker@sven.spflrc.org ls /home/walker/.www/images/photo/ > spflrclist.txt

ls images/photo > folderlist.txt

for file in $(diff --ignore-case spflrclist.txt folderlist.txt | sort | grep -i jpg | cut -c 3-19) ; do
	filelist="$filelist $file.jpg"
done

echo "files: $filelist"
echo ""
echo "proceed?"
read

cd /Users/walker/sites/birdwalker2/images/photo
scp $filelist walker@sven.spflrc.org:/home/walker/.www/images/photo
cd /Users/walker/sites/birdwalker2/images/thumb
scp $filelist walker@sven.spflrc.org:/home/walker/.www/images/thumb