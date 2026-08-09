plugins {
    id("myapp.android.library")
}

android {
    namespace = "com.nusatim.partner.features.projects.api"
}

dependencies {
    implementation(project(":navigation"))
}
