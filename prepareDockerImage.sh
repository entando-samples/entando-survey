#!/bin/sh

echo ""
echo "Building project and Docker image "
#todo export as global variable
#todo manage version
export MY_IMAGE=germanogiudici/entando-survey:0.0.15-SNAPSHOT

docker build -t ${MY_IMAGE} -f src/main/laravel/Dockerfile src/main/laravel
echo "Built $MY_IMAGE"

echo ""
echo "Uploading $MY_IMAGE to dockerhub"
docker push $MY_IMAGE

