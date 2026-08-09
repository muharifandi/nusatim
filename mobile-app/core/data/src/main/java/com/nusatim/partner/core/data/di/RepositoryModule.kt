package com.nusatim.partner.core.data.di

import com.nusatim.partner.core.data.repository.*
import com.nusatim.partner.core.domain.repository.*
import dagger.Binds
import dagger.Module
import dagger.hilt.InstallIn
import dagger.hilt.components.SingletonComponent
import javax.inject.Singleton

@Module
@InstallIn(SingletonComponent::class)
interface RepositoryModule {

    @Binds
    @Singleton
    fun bindAuthRepository(impl: AuthRepositoryImpl): AuthRepository

    @Binds
    @Singleton
    fun bindProfileRepository(impl: ProfileRepositoryImpl): ProfileRepository

    @Binds
    @Singleton
    fun bindLeadsRepository(impl: LeadsRepositoryImpl): LeadsRepository

    @Binds
    @Singleton
    fun bindCustomersRepository(impl: CustomersRepositoryImpl): CustomersRepository

    @Binds
    @Singleton
    fun bindProjectsRepository(impl: ProjectsRepositoryImpl): ProjectsRepository

    @Binds
    @Singleton
    fun bindCommissionsRepository(impl: CommissionsRepositoryImpl): CommissionsRepository

    @Binds
    @Singleton
    fun bindWithdrawalsRepository(impl: WithdrawalsRepositoryImpl): WithdrawalsRepository

    @Binds
    @Singleton
    fun bindMarketingRepository(impl: MarketingRepositoryImpl): MarketingRepository

    @Binds
    @Singleton
    fun bindSupportRepository(impl: SupportRepositoryImpl): SupportRepository

    @Binds
    @Singleton
    fun bindNotificationsRepository(impl: NotificationsRepositoryImpl): NotificationsRepository
}
