docker build -t labors_of_debugules .
docker run --name=labors_of_debugules --rm -p 80:3000 -p 445:445 -it labors_of_debugules