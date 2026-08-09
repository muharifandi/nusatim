import com.nusatim.partner.convention.libs
import org.gradle.api.Plugin
import org.gradle.api.Project
import org.gradle.kotlin.dsl.dependencies

class AndroidSecurityConventionPlugin : Plugin<Project> {
    override fun apply(target: Project) {
        with(target) {
            dependencies {
                // Crypto & Root Detection
                add("implementation", libs.findLibrary("androidx-security-crypto").get())
                add("implementation", libs.findLibrary("rootbeer").get())
                
                // Database Encryption (SQLCipher)
                add("implementation", libs.findLibrary("sqlcipher-android").get())
                add("implementation", libs.findLibrary("androidx-sqlite-ktx").get())
            }
        }
    }
}
