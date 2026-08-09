/**
 * Created by Muh. Arifandi on 07/05/26.
 * Email : arif76440@gmail.com
 * Project: My Application
 * File: core/common/build.gradle.kts
 */
plugins {
    id("myapp.android.library")
    id("myapp.android.hilt")
    id("myapp.android.security")
}

android {
    namespace = "com.nusatim.partner.core.common"
}

dependencies {
    api(project(":core:model"))
    api(project(":core:network"))
    
    implementation(libs.squareup.retrofit)
    implementation(libs.squareup.retrofit.gson)
    implementation(libs.squareup.okhttp.logging)

    implementation(libs.androidx.core.ktx)
    implementation(libs.androidx.appcompat)
    implementation(libs.androidx.lifecycle.runtime.ktx)
    implementation(libs.kotlinx.coroutines.android)
    implementation(libs.timber)
    implementation(libs.coil.kt)
    implementation(libs.coil.svg)

    testImplementation(libs.junit)
    testImplementation(libs.mockk)
    testImplementation(libs.kotlinx.coroutines.test)
    testImplementation(libs.turbine)
}
