import java.util.Properties
import java.io.FileInputStream

plugins {
    id("myapp.android.library")
    id("myapp.android.hilt")
    alias(libs.plugins.kotlin.serialization)
}

val envProperties = Properties().apply {
    val envFile = rootProject.file("config.env")
    if (envFile.exists()) {
        load(FileInputStream(envFile))
    }
}

android {
    namespace = "com.nusatim.partner.core.network"

    defaultConfig {
        consumerProguardFiles("consumer-rules.pro")
        val baseUrl = envProperties.getProperty("BASE_URL")

        // Strict validation for Release builds
        val taskNames = project.gradle.startParameter.taskNames
        val isRelease = taskNames.any { it.contains("Release", ignoreCase = true) }

        if (baseUrl.isNullOrEmpty()) {
            if (isRelease) throw GradleException("BASE_URL is missing in config.env for Release build!")
            else logger.warn("Warning: BASE_URL is not defined in config.env")
        }

        buildConfigField("String", "BASE_URL", "\"${baseUrl ?: ""}\"")
    }

    buildFeatures {
        buildConfig = true
    }
}

dependencies {
    api(project(":core:model"))

    implementation(libs.squareup.retrofit)
    implementation(libs.squareup.retrofit.gson)
    implementation(libs.squareup.okhttp.logging)
    implementation(libs.kotlinx.serialization.json)
}
