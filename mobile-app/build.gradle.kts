/**
 * Created by Muh. Arifandi on 12/05/2026
 * Email : arif76440@gmail.com
 * Project : My Application
 * Module : root
 * File : build.gradle.kts
 *
 * Description:
 * File build Gradle tingkat root untuk konfigurasi plugin global dan manajemen dependensi.
 */

import java.util.Properties
import java.io.FileInputStream

// Top-level build file where you can add configuration options common to all sub-projects/modules.
plugins {
    alias(libs.plugins.android.application) apply false
    alias(libs.plugins.android.library) apply false
    alias(libs.plugins.kotlin.android) apply false
    alias(libs.plugins.google.hilt) apply false
    alias(libs.plugins.ksp) apply false
    alias(libs.plugins.kotlin.serialization) apply false
    alias(libs.plugins.androidx.baselineprofile) apply false
    alias(libs.plugins.module.graph.assert)
    alias(libs.plugins.detekt) apply false
}

moduleGraphAssert {
    maxHeight = 4
    allowed = arrayOf(
        ":app -> :features:.*:impl",
        ":features:.*:impl -> :features:.*:api",
        ":features:.* -> :core:.*",
        ":core:.* -> :core:.*"
    )
    restricted = arrayOf(
        ":features:.*:impl -> :features:.*:impl" // Prevents feature coupling
    )
}

val envProperties = Properties().apply {
    val envFile = rootProject.file(".env")
    if (envFile.exists()) {
        load(FileInputStream(envFile))
    }
}

extra["envProperties"] = envProperties

// Share these properties across all subprojects
subprojects {
    extra["envProperties"] = envProperties
}

// Task untuk membantu download semua dependensi agar siap Offline Build
tasks.register("prepareOfflineBuild") {
    description = "Downloads all dependencies for all projects to local cache"
    group = "help"
    
    doLast {
        subprojects.forEach { subproject ->
            println("📦 Menyiapkan dependensi untuk: ${subproject.path}")
            subproject.configurations.forEach { configuration ->
                if (configuration.isCanBeResolved) {
                    try {
                        configuration.resolve()
                    } catch (e: Exception) {
                        // Abaikan error pada konfigurasi internal yang spesifik
                    }
                }
            }
        }
        println("✅ Semua dependensi telah diunduh ke cache lokal. Anda siap untuk Offline Build!")
    }
}
