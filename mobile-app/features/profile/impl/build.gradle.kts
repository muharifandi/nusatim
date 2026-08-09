plugins {
    id("myapp.android.feature")
    id("myapp.android.hilt")
}

android {
    namespace = "com.nusatim.partner.features.profile"
}

dependencies {
    implementation(project(":core:domain"))
    implementation(project(":core:architecture"))
    implementation(project(":core:ui"))
    implementation(project(":core:common"))
    implementation(project(":core:model"))
    implementation(project(":navigation"))
    implementation(libs.coil.kt)
    implementation(libs.androidx.swiperefreshlayout)

    testImplementation(libs.junit)
    testImplementation(libs.mockk)
    testImplementation(libs.kotlinx.coroutines.test)
    testImplementation(libs.turbine)

    androidTestImplementation(libs.androidx.junit)
    androidTestImplementation(libs.androidx.espresso.core)
    androidTestImplementation(project(":core:testing"))
    debugImplementation("androidx.fragment:fragment-testing:1.8.0")
}
