package com.nusatim.partner.features.register.di

import com.nusatim.partner.core.architecture.navigation.FeatureApi
import com.nusatim.partner.features.register.navigation.RegisterFeatureApiImpl
import dagger.Binds
import dagger.Module
import dagger.hilt.InstallIn
import dagger.hilt.components.SingletonComponent
import dagger.multibindings.IntoSet
import javax.inject.Singleton

@Module
@InstallIn(SingletonComponent::class)
interface RegisterNavigationModule {
    @Binds
    @IntoSet
    @Singleton
    fun bindRegisterFeatureApi(impl: RegisterFeatureApiImpl): FeatureApi
}
