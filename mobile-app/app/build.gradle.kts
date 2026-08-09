import java.util.Properties
import java.io.FileInputStream

plugins {
    id("myapp.android.application")
    id("myapp.android.hilt")
    id("myapp.android.room")
    id("myapp.android.paging")
    alias(libs.plugins.kotlin.serialization)
    alias(libs.plugins.module.graph.assert)
}

val envProperties = Properties().apply {
    val envFile = rootProject.file(".env")
    if (envFile.exists()) {
        load(FileInputStream(envFile))
    }
}

android {
    namespace = "com.nusatim.partner"

    defaultConfig {
        applicationId = envProperties.getProperty("APP_ID") ?: "com.nusatim.partner"
        
        versionCode = envProperties.getProperty("VERSION_CODE")?.toInt() ?: 1
        versionName = envProperties.getProperty("VERSION_NAME") ?: "1.0.0"

        testInstrumentationRunner = "androidx.test.runner.AndroidJUnitRunner"
        
        buildConfigField(
            "String",
            "NEWS_API_KEY",
            "\"${envProperties.getProperty("NEWS_API_KEY") ?: ""}\""
        )

        buildConfigField(
            "String",
            "BASE_URL",
            "\"${envProperties.getProperty("BASE_URL") ?: ""}\""
        )

        manifestPlaceholders["MAPS_API_KEY"] = envProperties.getProperty("MAPS_API_KEY") ?: ""
    }

    buildTypes {
        release {
            isMinifyEnabled = true
            isShrinkResources = true
            proguardFiles(
                getDefaultProguardFile("proguard-android-optimize.txt"),
                "proguard-rules.pro"
            )
        }
    }

    packaging {
        jniLibs {
            useLegacyPackaging = false
        }
        resources {
            excludes += "/META-INF/{AL2.0,LGPL2.1}"
            excludes += "META-INF/LICENSE.md"
            excludes += "META-INF/LICENSE-notice.md"
        }
    }

    buildFeatures {
        viewBinding = true
        dataBinding = true
    }
}

dependencies {
    implementation(project(":features:splash:impl"))
    implementation(project(":features:login:impl"))
    implementation(project(":features:register:impl"))
    implementation(project(":features:home:impl"))
    implementation(project(":features:intro:impl"))
    implementation(project(":features:profile:impl"))
    implementation(project(":features:leads:impl"))
    implementation(project(":features:projects:impl"))
    implementation(project(":features:finance:impl"))
    implementation(project(":features:auth:status"))
    
    implementation(project(":core:ui"))
    implementation(project(":core:common"))
    implementation(project(":core:network"))
    implementation(project(":core:architecture"))
    implementation(project(":core:domain"))
    implementation(project(":core:data"))
    implementation(project(":navigation"))

    implementation(libs.timber)

    implementation(libs.androidx.core.splashscreen)
    implementation(libs.androidx.appcompat)
    implementation(libs.material)
    implementation(libs.androidx.constraintlayout)
    implementation(libs.androidx.core.ktx)
    implementation(libs.androidx.lifecycle.runtime.ktx)
    implementation(libs.androidx.lifecycle.viewmodel.ktx)
    implementation(libs.androidx.lifecycle.livedata.ktx)
    
    testImplementation(libs.junit)
    androidTestImplementation(libs.androidx.espresso.core)
    androidTestImplementation(libs.androidx.junit)
    debugImplementation(libs.leakcanary.android)
    
    implementation(libs.androidx.navigation.fragment.ktx)
    implementation(libs.androidx.navigation.ui.ktx)
    implementation(libs.kotlinx.serialization.json)
    implementation(libs.coil.kt)
    
    implementation(libs.squareup.retrofit)
    implementation(libs.squareup.retrofit.gson)
    implementation(libs.squareup.okhttp.logging)
    implementation(libs.kotlinx.coroutines.android)
    
    testImplementation(libs.kotlinx.coroutines.test)
    testImplementation(libs.mockk)
    testImplementation(libs.turbine)
    testImplementation(libs.archunit)
    
    androidTestImplementation(project(":core:testing"))
}

moduleGraphAssert {
    maxHeight = 4
    // Strict rules to maintain the "Highly Scalable Engineering Foundation"
    allowed = arrayOf(
        ":app -> :navigation",
        ":app -> :features:.*:impl",
        ":app -> :core:.*",
        ":navigation -> :features:.*:api",
        ":features:.*:impl -> :features:.*:api",
        ":features:.*:impl -> :core:.*",
        ":features:.*:impl -> :navigation",
        ":core:.* -> :core:.*"
    )
    restricted = arrayOf(
        ":features:.*:api -> :features:.*:impl", // API cannot depend on Impl
        ":features:.*:impl -> :features:.*:impl" // Impl cannot depend on Impl
    )
}
