package com.nusatim.partner.features.leads.ui.customers.detail

import androidx.fragment.app.Fragment
import androidx.fragment.app.viewModels
import androidx.viewpager2.adapter.FragmentStateAdapter
import com.google.android.material.tabs.TabLayoutMediator
import com.nusatim.partner.core.architecture.base.BaseFragment
import com.nusatim.partner.features.leads.databinding.FragmentCustomerDetailBinding
import com.nusatim.partner.features.leads.ui.customers.detail.state.CustomerDetailIntent
import dagger.hilt.android.AndroidEntryPoint
import androidx.navigation.fragment.findNavController

@AndroidEntryPoint
class CustomerDetailFragment : BaseFragment<FragmentCustomerDetailBinding>() {

    private val viewModel: CustomerDetailViewModel by viewModels()
    private val customerId by lazy { arguments?.getInt("id") ?: -1 }

    override fun onInitViews() {
        binding.layoutToolbar.toolbar.apply {
            title = "Detail Customer"
            setNavigationOnClickListener {
                findNavController().navigateUp()
            }
        }

        if (customerId != -1) {
            viewModel.processIntent(CustomerDetailIntent.LoadCustomer(customerId))
        }

        setupViewPager()
    }

    private fun setupViewPager() {
        val adapter = object : FragmentStateAdapter(this) {
            override fun getItemCount(): Int = 4
            override fun createFragment(position: Int): Fragment {
                return when (position) {
                    0 -> CustomerInfoFragment()
                    1 -> CustomerTimelineFragment()
                    2 -> CustomerRemindersFragment()
                    else -> CustomerProposalFragment()
                }
            }
        }
        binding.viewPager.adapter = adapter

        TabLayoutMediator(binding.tabLayout, binding.viewPager) { tab, position ->
            tab.text = when (position) {
                0 -> getString(com.nusatim.partner.features.leads.R.string.lead_tab_info)
                1 -> getString(com.nusatim.partner.features.leads.R.string.lead_tab_timeline)
                2 -> getString(com.nusatim.partner.features.leads.R.string.customer_tab_followup)
                else -> getString(com.nusatim.partner.features.leads.R.string.customer_tab_proposal)
            }
        }.attach()
    }
}
