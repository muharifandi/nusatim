package com.nusatim.partner.core.data.di

import com.nusatim.partner.core.data.repository.AuthRepositoryImpl
import com.nusatim.partner.core.data.repository.CommissionsRepositoryImpl
import com.nusatim.partner.core.data.repository.CustomersRepositoryImpl
import com.nusatim.partner.core.data.repository.LeadsRepositoryImpl
import com.nusatim.partner.core.data.repository.MarketingRepositoryImpl
import com.nusatim.partner.core.data.repository.NotificationsRepositoryImpl
import com.nusatim.partner.core.data.repository.ProfileRepositoryImpl
import com.nusatim.partner.core.data.repository.ProjectsRepositoryImpl
import com.nusatim.partner.core.data.repository.WithdrawalsRepositoryImpl
import com.nusatim.partner.core.data.repository.SupportRepositoryImpl
import com.nusatim.partner.core.domain.repository.AuthRepository
import com.nusatim.partner.core.domain.repository.CommissionsRepository
import com.nusatim.partner.core.domain.repository.CustomersRepository
import com.nusatim.partner.core.domain.repository.LeadsRepository
import com.nusatim.partner.core.domain.repository.MarketingRepository
import com.nusatim.partner.core.domain.repository.NotificationsRepository
import com.nusatim.partner.core.domain.repository.ProfileRepository
import com.nusatim.partner.core.domain.repository.ProjectsRepository
import com.nusatim.partner.core.domain.repository.SupportRepository
import com.nusatim.partner.core.domain.repository.WithdrawalsRepository
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
