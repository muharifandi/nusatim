package com.nusatim.partner.features.finance.ui.commissions

import androidx.fragment.app.Fragment
import androidx.viewpager2.adapter.FragmentStateAdapter
import com.google.android.material.tabs.TabLayoutMediator
import com.nusatim.partner.core.architecture.base.BaseFragment
import com.nusatim.partner.features.finance.databinding.FragmentFinanceBinding
import com.nusatim.partner.features.finance.ui.withdrawals.WithdrawalsFragment
import dagger.hilt.android.AndroidEntryPoint

@AndroidEntryPoint
class FinanceFragment : BaseFragment<FragmentFinanceBinding>() {

    override fun onInitViews() {
        binding.layoutToolbar.toolbar.apply {
            title = "Keuangan"
            navigationIcon = null
        }
        setupViewPager()
    }

    private fun setupViewPager() {
        val adapter = object : FragmentStateAdapter(this) {
            override fun getItemCount(): Int = 2
            override fun createFragment(position: Int): Fragment {
                return when (position) {
                    0 -> CommissionsFragment()
                    else -> WithdrawalsFragment()
                }
            }
        }
        binding.viewPager.adapter = adapter

        TabLayoutMediator(binding.tabLayout, binding.viewPager) { tab, position ->
            tab.text = when (position) {
                0 -> "Komisi"
                else -> "Withdrawal"
            }
        }.attach()
    }
}
