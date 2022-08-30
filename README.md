# Features
Survey manager PBC, composed by a PHP back end microservice and two VUEJS microfrontends. Integration with keycloak and the postgres pod autodeployed by entando 


# Deployment and installation
With this configuration, you can use the ent cli (https://dev.entando.org/next/docs/reference/entando-cli.html) to perform the full deployment sequence:

# Development notes
## Tips
Whenever you are going to change the BE code, change the image version too in **prepareDockerImage.sh**
then be sure to align the version in **bundle_src/plugins/ent-survey-ms-plugin.yaml**
after the bundle build check that the version in **bundle/plugins/ent-survey-ms-plugin.yaml** is the same

for **PROD** build copy:
src/main/laravel/**env.prod** to src/main/laravel/.env
ui/widgets/widgets-dir/ent-survey-responses-widget/env.prod ui/widgets/widgets-dir/ent-survey-responses-widget/.env
ui/widgets/widgets-dir/ent-survey-widget/env.prod to ui/widgets/widgets-dir/ent-survey-widget/.env

for **LOCAL DEVELOPMENT** build copy:
src/main/laravel/**env.local** to src/main/laravel/.env
ui/widgets/widgets-dir/ent-survey-responses-widget/env.local ui/widgets/widgets-dir/ent-survey-responses-widget/.env
ui/widgets/widgets-dir/ent-survey-widget/env.local to ui/widgets/widgets-dir/ent-survey-widget/.env

if you have PHP locally installed, you can add the libraries to the laravel folder and uncomment the line `#- .:/var/www uncomment if you want to use local php files (php locally installed), otherwise you need to build the app at every change` in the src/main/laravel/docker-compose.yml to enabling **hot reload of php modification**. Otherwise, during local development, you'll need to build the entire BE docker image to check the modification running.  

### Setup the project directory.
1. Prepare the bundle directory: `cp -r bundle_src bundle`
2. Initialize the project: `ent prj init`
3. Initialize publication: `ent prj pbs-init` (requires the git bundle repo url)
4. Attach to kubernetes for an Entando application via ent attach-kubeconfig config-file or similar

### Publish the bundle.
1. Build FE: ent prj fe-build -a
2. Build and publish BE: ./prepareDockerImage.sh
3. Publish FE: `ent prj fe-push`
4. Deploy (after connecting to k8s above): `ent prj deploy`
5. Install the bundle via 1) App Builder, 2) `ent prj install`, or 3) `ent prj install --conflict-strategy=OVERRIDE` on subsequent installs.
6. Iterate steps 1-4 to publish new versions.

## Local testing of the project
**Warning** remember to copy the right .env file content as explained in the tips 
* `ent prj xk start` - or stop to shutdown keycloak again.
* `docker-compose up --build` `(docker-compose down)` from the src/main/laravel folder to run the BE **(see the tip to enable hot reload)**
* `ent prj fe-test-run` - to run the React frontend

## Local setup
* Access Keycloak at http://localhost:9080/auth/ (admin/admin)
* Use client web_app when authorizing the microservices

## Notes
* Two users are included in the keycloak realm config
  * test-user/test-user - regular user with no role provided (receive 403 on api call)
  * survey-admin/survey-admin - can access the api (role internal/survey-admin)
