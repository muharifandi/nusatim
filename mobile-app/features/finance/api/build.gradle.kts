plugins {
    id("myapp.android.library")
}

android {
    namespace = "com.nusatim.partner.features.finance.api"
}

dependencies {
    implementation(project(":navigation"))
}
