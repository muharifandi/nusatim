plugins {
    id("myapp.android.library")
    id("myapp.android.hilt")
}

android {
    namespace = "com.nusatim.partner.core.testing"
}

dependencies {
    implementation(project(":core:model"))
    api(libs.junit)
    api(libs.androidx.junit)
    api(libs.androidx.espresso.core)
    api("androidx.test.espresso:espresso-contrib:3.6.1")
    api(libs.androidx.constraintlayout) // Example common UI dep
    implementation("androidx.recyclerview:recyclerview:1.3.2")
    api(libs.kotlinx.coroutines.test)
    api(libs.mockk)
    api(libs.turbine)
    api(libs.archunit)
}
