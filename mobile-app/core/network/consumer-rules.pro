# Keep DTOs from being obfuscated
-keepclassmembers class com.nusatim.partner.core.network.dto.** { *; }

# Retrofit/OkHttp rules
-dontwarn retrofit2.**
-keep class retrofit2.** { *; }
-keepattributes Signature, InnerClasses, AnnotationDefault

-dontwarn okhttp3.**
-keep class okhttp3.** { *; }
-keep interface okhttp3.** { *; }
