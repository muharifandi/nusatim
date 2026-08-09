plugins {
    id("myapp.android.library")
}

android {
    namespace = "com.nusatim.partner.core.domain"
}

dependencies {
    implementation(project(":core:model"))
    implementation(libs.kotlinx.coroutines.core)
    implementation(libs.squareup.okhttp.logging) // for MultipartBody
}
