/**
 * Created by Muh. Arifandi on 12/05/2026
 * Email : arif76440@gmail.com
 * Project : My Application
 * Module : app
 * File : ConfigModule.kt
 *
 * Description:
 * Module Hilt untuk menyediakan konfigurasi dari BuildConfig (file .env) 
 * ke modul-modul lain melalui Dependency Injection.
 */

package com.nusatim.partner.di

import com.nusatim.partner.BuildConfig
import com.nusatim.partner.core.common.security.StringObfuscator
import dagger.Module
import dagger.Provides
import dagger.hilt.InstallIn
import dagger.hilt.components.SingletonComponent
import javax.inject.Named
import javax.inject.Singleton

@Module
@InstallIn(SingletonComponent::class)
object ConfigModule {

    @Provides
    @Singleton
    @Named("baseUrl")
    fun provideBaseUrl(): String = BuildConfig.BASE_URL

    @Provides
    @Singleton
    @Named("apiKey")
    fun provideApiKey(): String {
        // Return raw for now to match plain text in config.env
        return BuildConfig.NEWS_API_KEY
    }
}
