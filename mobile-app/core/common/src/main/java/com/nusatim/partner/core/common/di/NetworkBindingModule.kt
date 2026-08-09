package com.nusatim.partner.core.common.di

import com.nusatim.partner.core.common.network.InterceptorQualifier
import com.nusatim.partner.core.common.network.NetworkInterceptor
import dagger.Binds
import dagger.Module
import dagger.hilt.InstallIn
import dagger.hilt.components.SingletonComponent
import okhttp3.Interceptor
import javax.inject.Singleton

@Module
@InstallIn(SingletonComponent::class)
interface NetworkBindingModule {

    @Binds
    @Singleton
    @InterceptorQualifier
    fun bindNetworkInterceptor(impl: NetworkInterceptor): Interceptor
}
