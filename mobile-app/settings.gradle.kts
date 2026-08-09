pluginManagement {
    repositories {
        google {
            content {
                includeGroupByRegex("com\\.android.*")
                includeGroupByRegex("com\\.google.*")
                includeGroupByRegex("androidx.*")
            }
        }
        mavenCentral()
        gradlePluginPortal()
    }
}

plugins {
    id("org.gradle.toolchains.foojay-resolver-convention") version "1.0.0"
}

includeBuild("build-logic")

dependencyResolutionManagement {
    repositoriesMode.set(RepositoriesMode.FAIL_ON_PROJECT_REPOS)
    repositories {
        google()
        mavenCentral()
        maven { url = uri("https://jitpack.io") }
    }
}

rootProject.name = "Partnert"

// App & Navigation
include(":app")
include(":navigation")

// Core Modules
include(":core:common")
include(":core:architecture")
include(":core:domain")
include(":core:data")
include(":core:model")
include(":core:network")
include(":core:ui")
include(":core:testing")

// Feature Modules
include(":features:splash:impl")
include(":features:splash:api")
include(":features:login:api")
include(":features:login:impl")
include(":features:register:api")
include(":features:register:impl")
include(":features:leads:impl")
include(":features:leads:api")
include(":features:home:impl")
include(":features:intro:impl")
include(":features:profile:impl")
include(":features:auth:status")
include(":features:projects:api")
include(":features:projects:impl")
include(":features:finance:api")
include(":features:finance:impl")
