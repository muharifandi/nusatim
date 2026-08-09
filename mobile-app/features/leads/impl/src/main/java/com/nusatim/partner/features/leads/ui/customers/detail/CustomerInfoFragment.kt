package com.nusatim.partner.features.leads.ui.customers.detail

import androidx.core.net.toUri
import androidx.fragment.app.viewModels
import androidx.lifecycle.Lifecycle
import androidx.lifecycle.lifecycleScope
import androidx.lifecycle.repeatOnLifecycle
import androidx.navigation.fragment.findNavController
import com.nusatim.partner.core.architecture.base.BaseFragment
import com.nusatim.partner.core.model.dto.CustomerResponse
import com.nusatim.partner.features.leads.databinding.FragmentCustomerInfoBinding
import dagger.hilt.android.AndroidEntryPoint
import kotlinx.coroutines.launch
import java.text.NumberFormat
import java.util.*

@AndroidEntryPoint
class CustomerInfoFragment : BaseFragment<FragmentCustomerInfoBinding>() {

    private val viewModel: CustomerDetailViewModel by viewModels({ requireParentFragment() })

    override fun onInitViews() {
        binding.btnUpdateProgress.setOnClickListener {
            val customer = viewModel.state.value.customer ?: return@setOnClickListener
            val currentProgress = customer.project?.progress ?: 0
            showProgressDialog(customer.id, currentProgress)
        }
        
        binding.btnViewCommission.setOnClickListener {
            val customer = viewModel.state.value.customer ?: return@setOnClickListener
            customer.commission?.let {
                findNavController().navigate("partner://commissions/detail?id=${it.id}".toUri())
            }
        }
    }

    private fun showProgressDialog(customerId: Int, currentProgress: Int) {
        val slider = com.google.android.material.slider.Slider(requireContext()).apply {
            valueFrom = 0f
            valueTo = 100f
            stepSize = 5f
            value = currentProgress.toFloat()
        }

        com.google.android.material.dialog.MaterialAlertDialogBuilder(requireContext())
            .setTitle(getString(com.nusatim.partner.features.leads.R.string.customer_dialog_update_progress_title))
            .setView(slider)
            .setPositiveButton(getString(com.nusatim.partner.core.ui.R.string.error_retry)) { _, _ ->
                viewModel.processIntent(com.nusatim.partner.features.leads.ui.customers.detail.state.CustomerDetailIntent.UpdateProgress(customerId, slider.value.toInt()))
            }
            .setNegativeButton(getString(android.R.string.cancel), null)
            .show()
    }

    override fun onInitObservers() {
        viewLifecycleOwner.lifecycleScope.launch {
            viewLifecycleOwner.repeatOnLifecycle(Lifecycle.State.STARTED) {
                viewModel.state.collect { state ->
                    state.customer?.let { renderCustomer(it) }
                }
            }
        }
    }

    private fun renderCustomer(customer: CustomerResponse) {
        binding.tvName.text = customer.name ?: "-"
        binding.tvPic.text = getString(
            com.nusatim.partner.features.leads.R.string.customer_label_pic,
            customer.picName ?: "-",
            customer.picPhone ?: "-"
        )
        binding.tvProduct.text = customer.serviceName ?: "-"
        binding.tvProjectValue.text = formatRupiah(customer.projectValue)

        binding.btnPaymentStatus.text = customer.paymentStatus?.replaceFirstChar { it.uppercase() } ?: "-"
        val statusColor = when (customer.paymentStatus?.lowercase()) {
            "unpaid" -> com.nusatim.partner.core.ui.R.color.status_warning
            "partial" -> com.nusatim.partner.core.ui.R.color.status_info
            "paid" -> com.nusatim.partner.core.ui.R.color.status_success
            else -> com.nusatim.partner.core.ui.R.color.status_neutral
        }
        binding.btnPaymentStatus.setBackgroundTintList(android.content.res.ColorStateList.valueOf(
            androidx.core.content.ContextCompat.getColor(requireContext(), statusColor)
        ))

        customer.project?.let { project ->
            binding.cardProject.visibility = android.view.View.VISIBLE
            binding.tvProjectName.text = project.name
            binding.progressProject.progress = project.progress
            binding.tvProgressLabel.text = getString(
                com.nusatim.partner.features.leads.R.string.customer_label_progress_percent,
                project.progress
            )
        } ?: run {
            binding.cardProject.visibility = android.view.View.GONE
        }

        customer.commission?.let { commission ->
            binding.cardCommission.visibility = android.view.View.VISIBLE
            binding.tvCommissionAmount.text = formatRupiah(commission.amount)
        } ?: run {
            binding.cardCommission.visibility = android.view.View.GONE
        }
    }

    private fun formatRupiah(amount: Long): String {
        val format = NumberFormat.getCurrencyInstance(Locale("id", "ID"))
        return format.format(amount).replace(",00", "")
    }
}
