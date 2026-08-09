/**
 * Created by Muh. Arifandi on 12/05/2026
 * Email : arif76440@gmail.com
 * Project : My Application
 * Module : build-logic:convention
 * File : AndroidApplicationConventionPlugin.kt
 *
 * Description:
 * Plugin konvensi untuk modul aplikasi Android yang mengatur konfigurasi dasar SDK dan fitur build.
 */

import com.android.build.api.dsl.ApplicationExtension
import com.nusatim.partner.convention.configureKotlinAndroid
import org.gradle.api.Plugin
import org.gradle.api.Project
import org.gradle.kotlin.dsl.configure

class AndroidApplicationConventionPlugin : Plugin<Project> {
    override fun apply(target: Project) {
        with(target) {
            with(pluginManager) {
                apply("com.android.application")
                apply("org.jetbrains.kotlin.android")
                apply("myapp.android.detekt")
            }

            extensions.configure<ApplicationExtension> {
                configureKotlinAndroid(this)
                defaultConfig.targetSdk = 36
                buildFeatures {
                    buildConfig = true
                    viewBinding = true
                    dataBinding = true
                }
            }
        }
    }
}
