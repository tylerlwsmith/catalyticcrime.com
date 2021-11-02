pipeline {
    agent any
    // https://stackoverflow.com/questions/42309957/jenkins-can-comments-be-added-to-a-jenkinsfile
    // https://e.printstacktrace.blog/jenkins-pipeline-environment-variables-the-definitive-guide/
    environment {
        ENV_FILE = "${env.JENKINS_HOME}/env_files/catalyticcrime.com/.env"
        ENV_PRODUCTION_FILE = "${env.JENKINS_HOME}/env_files/catalyticcrime.com/.env.production"
        BUILD_TAG = "${sh(script: 'date -u +"%Y-%m-%d_%H-%M-%S"', returnStdout: true).trim()}"
        DOCKERHUB_KEY = credentials("dockerhub_key")
        MAILFROM = "Jenkins <tyler@catalyticcrime.com>"
        MAILTO = "tyler@deadhandmedia.com"
    }

    triggers {
      pollSCM "*/1 * * * *"
    }

    stages {
        stage("Meta") {
            steps {
                echo "Running ${env.BUILD_ID} on ${env.JENKINS_URL}"
                echo "${env.BUILD_ID}"
                echo "env: ${env.ENV_FILE}"
                sh "echo I am echoing ${env.BUILD_TAG}"
            }
        }
        stage("Verify .env files") {
            steps {
                script {
                    // https://support.cloudbees.com/hc/en-us/articles/360027607532-Pipeline-with-conditional-stages-based-on-a-file-existing-on-the-filesystem?page=31
                    if (!fileExists(ENV_FILE)) {
                        currentBuild.result = "ABORTED"
                        error("Missing .env file")
                    }
                }
            }
        }
        stage("Build") {
            steps {
                sh "ENV_FILE=${env.ENV_FILE} docker-compose --env-file ${env.ENV_FILE} -f docker-compose.build.yml build --build-arg build_tag=$BUILD_TAG"
            }
        }
        stage("Test Setup") {
            steps {
                sh "ENV_FILE=${env.ENV_FILE} docker-compose --env-file ${env.ENV_FILE} -f docker-compose.build.yml up -d"
            }
        }
        stage("Test") {
            failFast true
            parallel {
                stage("Test Web App") {
                    steps {
                        sh "docker exec catalytic-webapp php artisan test"
                    }
                }
            }
        }
        stage("Push to Registry") {
            when {
                branch "main"
            }
            steps {
                sh "echo $DOCKERHUB_KEY | docker login -u tylerlwsmith --password-stdin"
                sh "ENV_FILE=${env.ENV_FILE} docker-compose --env-file ${env.ENV_FILE} -f docker-compose.build.yml push"
            }
        }
        stage("Deploy") {
            when {
                branch "main"
            }
            environment {
                DOCKER_HOST="ssh://root@ssh.catalyticcrime.com" // TODO: create unprivileged user.
                DOCKER_TLS_VERIFY=0
                COMPOSE_DOCKER_CLI_BUILD=0
            }
            steps {
                sh "docker-compose pull --env-file ${env.ENV_FILE}"
                sh "ENV_FILE=${env.ENV_FILE} docker-compose --env-file ${env.ENV_FILE} -f docker-compose.prod.yml up -d"
            }
        }
    }
    post {
        always {
            echo "Cleanup"
            sh "docker kill \$(docker ps -q)" // This is a hack but it works.
        }
        success {
            emailext from: "${MAILFROM}",
            to: "${MAILTO}",
            subject: "Build success: ${currentBuild.fullDisplayName}",
            body: "See results at ${env.BUILD_URL}"
        }
        failure {
            emailext from: "${MAILFROM}",
            to: "${MAILTO}",
            subject: "Build failure: ${currentBuild.fullDisplayName}",
            body: "See results at ${env.BUILD_URL}"
        }
        unstable {
            emailext from: "${MAILFROM}",
            to: "${MAILTO}",
            subject: "Build unstable: ${currentBuild.fullDisplayName}",
            body: "See results at ${env.BUILD_URL}"
        }
    }
}
