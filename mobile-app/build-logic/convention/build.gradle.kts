plugins {
    `kotlin-dsl`
}

group = "com.nusatim.partner.convention"

dependencies {
    compileOnly(libs.android.gradlePlugin)
    compileOnly(libs.kotlin.gradlePlugin)
    compileOnly(libs.detekt.gradlePlugin)
    compileOnly("com.google.dagger:hilt-android-gradle-plugin:2.51.1")
    compileOnly("com.google.devtools.ksp:com.google.devtools.ksp.gradle.plugin:2.0.0-1.0.21")
}

gradlePlugin {
    plugins {
        register("androidApplication") {
            id = "myapp.android.application"
            implementationClass = "AndroidApplicationConventionPlugin"
        }
        register("androidLibrary") {
            id = "myapp.android.library"
            implementationClass = "AndroidLibraryConventionPlugin"
        }
        register("kotlinLibrary") {
            id = "myapp.kotlin.library"
            implementationClass = "KotlinLibraryConventionPlugin"
        }
        register("androidFeature") {
            id = "myapp.android.feature"
            implementationClass = "AndroidFeatureConventionPlugin"
        }
        register("androidHilt") {
            id = "myapp.android.hilt"
            implementationClass = "AndroidHiltConventionPlugin"
        }
        register("androidRoom") {
            id = "myapp.android.room"
            implementationClass = "AndroidRoomConventionPlugin"
        }
        register("androidPaging") {
            id = "myapp.android.paging"
            implementationClass = "AndroidPagingConventionPlugin"
        }
        register("androidSecurity") {
            id = "myapp.android.security"
            implementationClass = "AndroidSecurityConventionPlugin"
        }
        register("androidDetekt") {
            id = "myapp.android.detekt"
            implementationClass = "AndroidDetektConventionPlugin"
        }
    }
}
