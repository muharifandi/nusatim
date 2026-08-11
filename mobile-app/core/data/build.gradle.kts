plugins {
    id("myapp.android.library")
    id("myapp.android.hilt")
    id("myapp.android.room")
}

android {
    namespace = "com.nusatim.partner.core.data"
}

dependencies {
    implementation(project(":core:domain"))
    implementation(project(":core:network"))
    implementation(project(":core:model"))

    implementation(libs.squareup.retrofit)
    implementation(libs.squareup.retrofit.gson)

    testImplementation(libs.junit)
    testImplementation(libs.mockk)
    testImplementation(libs.kotlinx.coroutines.test)
    testImplementation(libs.turbine)
}
