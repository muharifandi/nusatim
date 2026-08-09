package com.nusatim.partner.convention

import com.android.build.api.dsl.CommonExtension
import org.gradle.api.JavaVersion
import org.gradle.api.Project
import org.gradle.api.plugins.JavaPluginExtension
import org.gradle.kotlin.dsl.configure
import org.gradle.kotlin.dsl.withType
import org.jetbrains.kotlin.gradle.dsl.JvmTarget
import org.jetbrains.kotlin.gradle.tasks.KotlinCompile

/**
 * Configure base Kotlin with Android options
 */
internal fun Project.configureKotlinAndroid(
    commonExtension: CommonExtension<*, *, *, *, *, *>,
) {
    commonExtension.apply {
        compileSdk = 36

        defaultConfig {
            minSdk = 24
            
            // Konfigurasi flag linker untuk 16 KB page alignment
            externalNativeBuild {
                cmake {
                    arguments("-DANDROID_EXT_LINKER_FLAGS=-Wl,-z,max-page-size=16384")
                }
            }
        }

        compileOptions {
            sourceCompatibility = JavaVersion.VERSION_17
            targetCompatibility = JavaVersion.VERSION_17
        }

        packaging {
            jniLibs {
                useLegacyPackaging = false
            }
        }

        testOptions {
            unitTests.isReturnDefaultValues = true
        }
    }

    configureKotlin()
}

/**
 * Configure base Kotlin options for JVM (non-Android)
 */
internal fun Project.configureKotlinJvm() {
    extensions.configure<JavaPluginExtension> {
        sourceCompatibility = JavaVersion.VERSION_17
        targetCompatibility = JavaVersion.VERSION_17
    }

    configureKotlin()
}

/**
 * Configure base Kotlin options
 */
private fun Project.configureKotlin() {
    // Use withType to workaround across project targets
    tasks.withType<KotlinCompile>().configureEach {
        compilerOptions {
            // Set JVM target to 17
            jvmTarget.set(JvmTarget.JVM_17)
        }
    }
}
