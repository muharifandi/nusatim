plugins {
    id("myapp.android.feature")
    id("myapp.android.hilt")
}

android {
    namespace = "com.nusatim.partner.features.auth.status"
}

dependencies {
    implementation(project(":core:domain"))
    implementation(project(":core:architecture"))
    implementation(project(":core:ui"))
    implementation(project(":core:common"))
    implementation(project(":core:model"))
    implementation(project(":navigation"))
}
