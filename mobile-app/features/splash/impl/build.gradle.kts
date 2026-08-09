plugins {
    id("myapp.android.feature")
    id("myapp.android.hilt")
}

android {
    namespace = "com.nusatim.partner.features.splash"
}

dependencies {
    implementation(project(":core:domain"))
    implementation(project(":features:splash:api"))
    implementation(project(":features:login:api"))
    implementation(project(":core:architecture"))
    implementation(project(":navigation"))

    androidTestImplementation(libs.androidx.junit)
    androidTestImplementation(libs.mockk)
}
