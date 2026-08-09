import com.nusatim.partner.convention.libs
import org.gradle.api.Plugin
import org.gradle.api.Project
import org.gradle.kotlin.dsl.configure
import org.gradle.kotlin.dsl.dependencies
import com.google.devtools.ksp.gradle.KspExtension

class AndroidHiltConventionPlugin : Plugin<Project> {
    override fun apply(target: Project) {
        with(target) {
            with(pluginManager) {
                apply("com.google.dagger.hilt.android")
                apply("com.google.devtools.ksp")
            }

            extensions.configure<KspExtension> {
                arg("dagger.hilt.internal.useAggregatingRootProcessor", "true")
                arg("dagger.fastInit", "enabled")
                arg("dagger.hilt.android.internal.disableAndroidSuperclassValidation", "false")
                
                val projectType = if (pluginManager.hasPlugin("com.android.application")) "APP" else "LIB"
                arg("dagger.hilt.android.internal.projectType", projectType)
            }

            dependencies {
                "implementation"(libs.findLibrary("google.hilt.android").get())
                "ksp"(libs.findLibrary("google.hilt.compiler").get())
            }
        }
    }
}
