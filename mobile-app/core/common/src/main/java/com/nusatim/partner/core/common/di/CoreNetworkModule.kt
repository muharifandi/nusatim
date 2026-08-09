package com.nusatim.partner.core.common.di

import com.nusatim.partner.core.common.network.InterceptorQualifier
import com.nusatim.partner.core.network.AuthApiService
import com.nusatim.partner.core.network.BuildConfig
import com.nusatim.partner.core.network.CommissionsApiService
import com.nusatim.partner.core.network.CustomersApiService
import com.nusatim.partner.core.network.LeadsApiService
import com.nusatim.partner.core.network.MarketingApiService
import com.nusatim.partner.core.network.NotificationsApiService
import com.nusatim.partner.core.network.ProfileApiService
import com.nusatim.partner.core.network.ProjectsApiService
import com.nusatim.partner.core.network.SupportApiService
import com.nusatim.partner.core.network.WithdrawalsApiService
import coil.ImageLoader
import coil.decode.SvgDecoder
import coil.util.DebugLogger
import android.content.Context
import dagger.Module
import dagger.Provides
import dagger.hilt.InstallIn
import dagger.hilt.android.qualifiers.ApplicationContext
import dagger.hilt.components.SingletonComponent
import okhttp3.Interceptor
import okhttp3.OkHttpClient
import okhttp3.logging.HttpLoggingInterceptor
import retrofit2.Retrofit
import retrofit2.converter.gson.GsonConverterFactory
import java.util.concurrent.TimeUnit
import javax.inject.Singleton

@Module
@InstallIn(SingletonComponent::class)
object CoreNetworkModule {

    @Provides
    @Singleton
    fun provideLoggingInterceptor(): HttpLoggingInterceptor {
        return HttpLoggingInterceptor().apply {
            level = if (BuildConfig.DEBUG) HttpLoggingInterceptor.Level.BODY 
                    else HttpLoggingInterceptor.Level.NONE
        }
    }

    @Provides
    @Singleton
    fun provideOkHttpClient(
        loggingInterceptor: HttpLoggingInterceptor,
        @InterceptorQualifier networkInterceptor: Interceptor
    ): OkHttpClient {
        return OkHttpClient.Builder()
            .addInterceptor(networkInterceptor)
            .addInterceptor(loggingInterceptor)
            .connectTimeout(30, TimeUnit.SECONDS)
            .readTimeout(30, TimeUnit.SECONDS)
            .writeTimeout(30, TimeUnit.SECONDS)
            .build()
    }

    @Provides
    @Singleton
    fun provideImageLoader(
        @ApplicationContext context: Context,
        okHttpClient: OkHttpClient
    ): ImageLoader {
        return ImageLoader.Builder(context)
            .okHttpClient(okHttpClient)
            .components {
                add(SvgDecoder.Factory())
            }
            .crossfade(true)
            .allowHardware(false)
            .apply {
                if (BuildConfig.DEBUG) {
                    logger(DebugLogger())
                }
            }
            .build()
    }

    @Provides
    @Singleton
    fun provideRetrofit(okHttpClient: OkHttpClient): Retrofit {
        return Retrofit.Builder()
            .baseUrl(BuildConfig.BASE_URL)
            .client(okHttpClient)
            .addConverterFactory(GsonConverterFactory.create())
            .build()
    }

    @Provides
    @Singleton
    fun provideAuthApiService(retrofit: Retrofit): AuthApiService {
        return retrofit.create(AuthApiService::class.java)
    }

    @Provides
    @Singleton
    fun provideProfileApiService(retrofit: Retrofit): ProfileApiService {
        return retrofit.create(ProfileApiService::class.java)
    }

    @Provides
    @Singleton
    fun provideLeadsApiService(retrofit: Retrofit): LeadsApiService {
        return retrofit.create(LeadsApiService::class.java)
    }

    @Provides
    @Singleton
    fun provideCustomersApiService(retrofit: Retrofit): CustomersApiService {
        return retrofit.create(CustomersApiService::class.java)
    }

    @Provides
    @Singleton
    fun provideProjectsApiService(retrofit: Retrofit): ProjectsApiService {
        return retrofit.create(ProjectsApiService::class.java)
    }

    @Provides
    @Singleton
    fun provideCommissionsApiService(retrofit: Retrofit): CommissionsApiService {
        return retrofit.create(CommissionsApiService::class.java)
    }

    @Provides
    @Singleton
    fun provideWithdrawalsApiService(retrofit: Retrofit): WithdrawalsApiService {
        return retrofit.create(WithdrawalsApiService::class.java)
    }

    @Provides
    @Singleton
    fun provideMarketingApiService(retrofit: Retrofit): MarketingApiService {
        return retrofit.create(MarketingApiService::class.java)
    }

    @Provides
    @Singleton
    fun provideSupportApiService(retrofit: Retrofit): SupportApiService {
        return retrofit.create(SupportApiService::class.java)
    }

    @Provides
    @Singleton
    fun provideNotificationsApiService(retrofit: Retrofit): NotificationsApiService {
        return retrofit.create(NotificationsApiService::class.java)
    }
}
