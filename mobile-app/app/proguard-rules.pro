# Add project specific ProGuard rules here.
# You can control the set of applied configuration files using the
# proguardFiles setting in build.gradle.
#
# For more details, see
#   http://developer.android.com/guide/developing/tools/proguard.html

# Hide source file and line number for security
-renamesourcefileattribute SourceFile
-keepattributes SourceFile,LineNumberTable

# Hardening for Domain and Repository names
-keep class com.nusatim.partner.core.model.** { *; }
-keep class com.nusatim.partner.features.news.domain.model.** { *; }

# Obfuscate implementation details
-repackageclasses ''
-allowaccessmodification

# Remove Log calls in production for security
-assumenosideeffects class android.util.Log {
    public static *** d(...);
    public static *** v(...);
    public static *** i(...);
}

# SQLCipher rules
-keep class net.sqlcipher.** { *; }
-keep class net.sqlcipher.database.** { *; }

# Security Hardening: Protect sensitive logic
-keep class com.nusatim.partner.core.common.security.StringObfuscator { *; }
-keep class com.nusatim.partner.core.common.security.SecurityProvider {
    public boolean isDeviceRooted();
    public boolean isHookingDetected();
    public boolean isRunningOnEmulator();
}
