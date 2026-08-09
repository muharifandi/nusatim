plugins {
    id("myapp.android.library")
}

android {
    namespace = "com.nusatim.partner.core.ui"
    buildFeatures {
        viewBinding = true
    }
}

dependencies {
    implementation(project(":core:common"))
    implementation(libs.androidx.appcompat)
    implementation(libs.material)
}
